<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:generate-initial-slugs')]
#[Description('Generate slugs for existing categories and products')]
class GenerateInitialSlugs extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Generate slugs for categories
        $categories = Category::whereNull('slug')->get();
        $this->info("Found {$categories->count()} categories without slugs.");
        
        foreach ($categories as $category) {
            $category->slug = Category::generateUniqueSlug($category->name);
            $category->save();
            $this->line("Generated slug for category: {$category->name} -> {$category->slug}");
        }
        
        // Generate slugs for products
        $products = Product::whereNull('slug')->get();
        $this->info("\nFound {$products->count()} products without slugs.");
        
        foreach ($products as $product) {
            $product->slug = Product::generateUniqueSlug($product->name);
            $product->save();
            $this->line("Generated slug for product: {$product->name} -> {$product->slug}");
        }
        
        $this->info("\nAll slugs generated successfully!");
    }
}
