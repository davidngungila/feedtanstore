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
        $categories = Category::whereNull('slug')->orWhere('slug', '')->get();
        $this->info("Found {$categories->count()} categories without slugs.");
        
        foreach ($categories as $category) {
            $category->save(); // The model's saving event will generate the slug
            $this->line("Generated slug for category: {$category->name} -> {$category->slug}");
        }
        
        // Generate slugs for products
        $products = Product::whereNull('slug')->orWhere('slug', '')->get();
        $this->info("\nFound {$products->count()} products without slugs.");
        
        foreach ($products as $product) {
            $product->save(); // The model's saving event will generate the slug
            $this->line("Generated slug for product: {$product->name} -> {$product->slug}");
        }
        
        $this->info("\nAll slugs generated successfully!");
    }
}
