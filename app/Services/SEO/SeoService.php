<?php

namespace App\Services\SEO;

class SeoService
{
    public static function meta($title, $description, $image = null, $keywords = [])
    {
        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'keywords' => implode(',', $keywords),
        ];
    }

    public static function product($product)
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        
        // Get primary product image
        $primaryImage = $product->images->firstWhere('is_primary', true);
        $imageUrl = $primaryImage?->image_path ?? $product->image;
        
        // Resolve image URL
        if ($imageUrl) {
            if (!str_starts_with($imageUrl, 'http://') && !str_starts_with($imageUrl, 'https://')) {
                $cleanPath = ltrim($imageUrl, '/');
                if (str_starts_with($cleanPath, 'storage/')) {
                    $imageUrl = rtrim($baseUrl, '/') . '/' . $cleanPath;
                } else {
                    $imageUrl = rtrim($baseUrl, '/') . '/storage/' . $cleanPath;
                }
            }
        } else {
            $imageUrl = $baseUrl . '/logo-image-feedtan-store.png';
        }

        $categoryName = $product->category?->name ?? 'Products';
        
        return self::meta(
            "Buy {$product->name} in Moshi | FeedTan Store",
            "Order {$product->name} online at FeedTan Store. Fast delivery in Moshi, Kilimanjaro, Tanzania.",
            $imageUrl,
            [
                $product->name,
                $categoryName,
                'Moshi supermarket',
                'Kilimanjaro grocery delivery',
                'online shopping Moshi',
                'FeedTan Store'
            ]
        );
    }

    public static function shopIndex($selectedCategory = null, $searchTerm = null)
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        
        if ($selectedCategory) {
            return self::meta(
                "{$selectedCategory->name} - FeedTan Store Online Shop Moshi Kilimanjaro",
                "Browse {$selectedCategory->name} at FeedTan Store with trusted prices, secure checkout, and delivery options across Moshi, Kilimanjaro, Tanzania.",
                $baseUrl . '/logo-image-feedtan-store.png',
                [
                    $selectedCategory->name,
                    'Moshi supermarket',
                    'Kilimanjaro grocery delivery',
                    'FeedTan Store'
                ]
            );
        }
        
        if ($searchTerm) {
            return self::meta(
                "Search results for \"{$searchTerm}\" - FeedTan Store Moshi",
                "Find products matching \"{$searchTerm}\" at FeedTan Store and order online with quick checkout and delivery tracking in Moshi, Kilimanjaro.",
                $baseUrl . '/logo-image-feedtan-store.png',
                [
                    $searchTerm,
                    'Moshi supermarket',
                    'Kilimanjaro grocery delivery',
                    'FeedTan Store'
                ]
            );
        }
        
        return self::meta(
            'Moshi Online Supermarket | Grocery Delivery in Kilimanjaro Tanzania | FeedTan Store',
            'Shop groceries online in Moshi, Kilimanjaro. Order fresh food, household items, and get fast home delivery across Moshi town with FeedTan Store.',
            $baseUrl . '/logo-image-feedtan-store.png',
            [
                'online supermarket Moshi',
                'Moshi online grocery store',
                'supermarket delivery Moshi Kilimanjaro',
                'buy groceries online Moshi',
                'online shopping Moshi Tanzania',
                'grocery delivery Moshi',
                'food delivery supermarket Moshi',
                'Kilimanjaro online supermarket',
                'Moshi grocery delivery service',
                'online supermarket in Moshi',
                'grocery store near me Moshi',
                'food delivery near Moshi town',
                'supermarket in Kilimanjaro Tanzania',
                'Moshi town online shopping',
                'delivery supermarket Kilimanjaro region',
                'Moshi fresh food delivery',
                'Moshi household shopping online',
                'buy rice online Moshi',
                'Moshi maize flour delivery',
                'online vegetables supermarket Moshi',
                'fresh fruits delivery Moshi Tanzania',
                'cooking oil delivery Moshi',
                'dairy products online Moshi',
                'beverages delivery Moshi supermarket',
                'same day delivery Moshi supermarket',
                'home delivery grocery Moshi',
                'fast delivery Kilimanjaro supermarket',
                'affordable grocery delivery Moshi',
                'online order and home delivery Moshi',
                'FeedTan supermarket Moshi',
                'FeedTan grocery delivery Tanzania',
                'FeedTan online shopping platform',
                'FeedTan Kilimanjaro delivery service',
                'best online supermarket for home delivery in Moshi Tanzania',
                'how to buy groceries online in Moshi Kilimanjaro',
                'cheap grocery delivery service in Moshi town',
                'trusted online supermarket in Kilimanjaro region Tanzania'
            ]
        );
    }
}
