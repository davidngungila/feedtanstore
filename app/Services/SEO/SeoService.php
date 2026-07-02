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
            "Buy {$product->name} in Moshi | Feedtan Store",
            "Order {$product->name} online at Feedtan Store. Fast delivery in Moshi, Kilimanjaro, Tanzania.",
            $imageUrl,
            [
                $product->name,
                $categoryName,
                'Moshi store',
                'Kilimanjaro grocery delivery',
                'online shopping Moshi',
                'Feedtan Store'
            ]
        );
    }

    public static function shopIndex($selectedCategory = null, $searchTerm = null)
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        
        if ($selectedCategory) {
            return self::meta(
                "{$selectedCategory->name} - Feedtan Store Online Shop Moshi Kilimanjaro",
                "Browse {$selectedCategory->name} at Feedtan Store with trusted prices, secure checkout, and delivery options across Moshi, Kilimanjaro, Tanzania.",
                $baseUrl . '/logo-image-feedtan-store.png',
                [
                    $selectedCategory->name,
                    'Moshi store',
                    'Kilimanjaro grocery delivery',
                    'Feedtan Store'
                ]
            );
        }
        
        if ($searchTerm) {
            return self::meta(
                "Search results for \"{$searchTerm}\" - Feedtan Store Moshi",
                "Find products matching \"{$searchTerm}\" at Feedtan Store and order online with quick checkout and delivery tracking in Moshi, Kilimanjaro.",
                $baseUrl . '/logo-image-feedtan-store.png',
                [
                    $searchTerm,
                    'Moshi store',
                    'Kilimanjaro grocery delivery',
                    'Feedtan Store'
                ]
            );
        }
        
        return self::meta(
            'Moshi Online Store | Grocery Delivery in Kilimanjaro Tanzania',
            'Shop online in Moshi, Kilimanjaro, Tanzania. Order fresh food, household items, electronics, clothing, and get fast home delivery with Feedtan Store.',
            $baseUrl . '/logo-image-feedtan-store.png',
            [
                'online store Moshi',
                'Moshi online grocery store',
                'store delivery Moshi Kilimanjaro',
                'buy groceries online Moshi',
                'online shopping Moshi Tanzania',
                'grocery delivery Moshi',
                'food delivery Moshi',
                'Kilimanjaro online store',
                'Moshi grocery delivery service',
                'online store in Moshi',
                'grocery store near me Moshi',
                'food delivery near Moshi town',
                'store in Kilimanjaro Tanzania',
                'Moshi town online shopping',
                'delivery store Kilimanjaro region',
                'Moshi fresh food delivery',
                'Moshi household shopping online',
                'buy rice online Moshi',
                'Moshi maize flour delivery',
                'online vegetables Moshi',
                'fresh fruits delivery Moshi Tanzania',
                'cooking oil delivery Moshi',
                'dairy products online Moshi',
                'beverages delivery Moshi',
                'same day delivery Moshi',
                'home delivery grocery Moshi',
                'fast delivery Kilimanjaro',
                'affordable grocery delivery Moshi',
                'online order and home delivery Moshi',
                'Feedtan store Moshi',
                'Feedtan grocery delivery Tanzania',
                'Feedtan online shopping platform',
                'Feedtan Kilimanjaro delivery service',
                'best online store for home delivery in Moshi Tanzania',
                'how to buy groceries online in Moshi Kilimanjaro',
                'cheap grocery delivery service in Moshi town',
                'trusted online store in Kilimanjaro region Tanzania'
            ]
        );
    }
}
