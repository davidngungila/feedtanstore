<?php

namespace App\Services\SEO;

class SchemaService
{
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

        $availability = $product->quantity > 0 
            ? 'https://schema.org/InStock' 
            : 'https://schema.org/OutOfStock';

        return json_encode([
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $product->name,
            "image" => $imageUrl,
            "description" => $product->description ?? "Buy {$product->name} at FeedTan Store, Moshi's trusted online supermarket.",
            "brand" => [
                "@type" => "Brand",
                "name" => $product->brand?->name ?? "FeedTan Store"
            ],
            "offers" => [
                "@type" => "Offer",
                "price" => $product->selling_price,
                "priceCurrency" => "TZS",
                "availability" => $availability,
                "url" => url('/shop/product/' . $product->slug),
                "priceValidUntil" => now()->addYear()->toDateString()
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function organization()
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        
        return json_encode([
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => "FeedTan Store",
            "url" => $baseUrl,
            "logo" => $baseUrl . '/logo-image-feedtan-store.png',
            "contactPoint" => [
                "@type" => "ContactPoint",
                "telephone" => "+255717358865",
                "contactType" => "customer service",
                "availableLanguage" => ["English", "Swahili"]
            ],
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "Kiboriloni",
                "addressLocality" => "Moshi",
                "addressRegion" => "Kilimanjaro",
                "addressCountry" => "TZ"
            ],
            "sameAs" => [
                "https://facebook.com/feedtanstore",
                "https://instagram.com/feedtanstore"
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function webSite()
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        
        return json_encode([
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => "FeedTan Store - Moshi Online Supermarket",
            "url" => $baseUrl,
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => $baseUrl . "/shop?search={search_term_string}",
                "query-input" => "required name=search_term_string"
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
