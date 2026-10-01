<?php

use App\Services\SEO\SeoService;

if (!function_exists('seo_product')) {
    function seo_product($product)
    {
        return SeoService::product($product);
    }
}

if (!function_exists('seo_shop_index')) {
    function seo_shop_index($selectedCategory = null, $searchTerm = null)
    {
        return SeoService::shopIndex($selectedCategory, $searchTerm);
    }
}

if (!function_exists('shop_image_url')) {
    /**
     * Resolve a stored product image path to a full public URL.
     */
    function shop_image_url($path)
    {
        if (!$path) {
            return null;
        }

        static $baseUrl = null;
        if ($baseUrl === null) {
            try {
                $settings = \App\Models\StoreSetting::first();
                $baseUrl = rtrim($settings->store_url ?? config('app.url'), '/');
            } catch (\Throwable $e) {
                $baseUrl = rtrim((string) config('app.url'), '/');
            }
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path);
            if (isset($parsed['path'])) {
                $path = ltrim($parsed['path'], '/');
                return str_starts_with($path, 'storage/')
                    ? $baseUrl . '/' . $path
                    : $baseUrl . '/storage/' . $path;
            }
        }

        $cleanPath = ltrim($path, '/');

        return str_starts_with($cleanPath, 'storage/')
            ? $baseUrl . '/' . $cleanPath
            : $baseUrl . '/storage/' . $cleanPath;
    }
}
