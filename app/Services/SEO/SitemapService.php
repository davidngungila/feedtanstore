<?php

namespace App\Services\SEO;

use App\Models\Product;
use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;

class SitemapService
{
    public static function generate()
    {
        $settings = StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        $baseUrl = rtrim($baseUrl, '/'); // Ensure no trailing slash

        $urls = [];

        // Homepage
        $urls[] = [
            'loc' => $baseUrl . '/',
            'lastmod' => now()->toW3cString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // Shop
        $urls[] = [
            'loc' => $baseUrl . '/shop',
            'lastmod' => now()->toW3cString(),
            'changefreq' => 'daily',
            'priority' => '0.9'
        ];

        // Categories
        foreach (Category::where('is_active', true)->get() as $cat) {
            $urls[] = [
                'loc' => $baseUrl . '/shop?category=' . $cat->slug,
                'lastmod' => $cat->updated_at->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        // Products
        foreach (Product::where('is_active', true)->where('is_available_online', true)->get() as $product) {
            $urls[] = [
                'loc' => $baseUrl . '/shop/product/' . $product->slug,
                'lastmod' => $product->updated_at->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        return $urls;
    }

    public static function generateAndSave()
    {
        $settings = StoreSetting::firstOrCreate();
        $sitemapContent = view('sitemap', ['urls' => self::generate()])->render();
        
        // Explicitly overwrite the existing file with exclusive lock to prevent race conditions
        file_put_contents(public_path('sitemap.xml'), $sitemapContent, LOCK_EX);
        
        // Update last generated time
        $settings->update(['sitemap_last_generated_at' => now()]);
        
        // Automatically ping search engines
        self::pingSearchEngines();
    }

    protected static function pingSearchEngines()
    {
        $settings = StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        $sitemapUrl = rtrim($baseUrl, '/') . '/sitemap.xml';

        $searchEngines = [
            'Google' => 'https://www.google.com/ping?sitemap=',
            'Bing' => 'https://www.bing.com/ping?sitemap=',
            'Yandex' => 'https://webmaster.yandex.com/ping?sitemap='
        ];

        $statuses = [];

        foreach ($searchEngines as $name => $pingUrl) {
            try {
                $response = Http::get($pingUrl . urlencode($sitemapUrl));
                $statuses[$name] = [
                    'success' => $response->successful(),
                    'status_code' => $response->status(),
                    'timestamp' => now()->toW3cString(),
                    'message' => $response->successful() ? 'Successfully pinged' : 'Ping failed'
                ];
            } catch (\Exception $e) {
                \Log::error("Failed to ping {$name}: " . $e->getMessage());
                $statuses[$name] = [
                    'success' => false,
                    'status_code' => null,
                    'timestamp' => now()->toW3cString(),
                    'message' => $e->getMessage()
                ];
            }
        }

        $settings->update(['sitemap_search_engine_status' => $statuses]);
    }
}
