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
