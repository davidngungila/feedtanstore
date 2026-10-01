<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $categoryFilter = trim((string) $request->input('category', ''));
        $availability = $request->input('availability', 'all');
        $availability = in_array($availability, ['all', 'online', 'offline'], true) ? $availability : 'all';
        $imageFilter = $request->input('image', 'all');
        $imageFilter = in_array($imageFilter, ['all', 'with', 'without'], true) ? $imageFilter : 'all';

        $baseQuery = Product::query();
        $totalCount = (clone $baseQuery)->count();
        $onlineCount = (clone $baseQuery)->where('is_available_online', true)->count();

        $selectedCategory = $this->resolveCatalogCategory($categoryFilter);
        $applyFilters = fn ($query, array $except = []) => $this->applyCatalogFilters($query, $search, $selectedCategory, $availability, $imageFilter, $except);
        $filteredQuery = fn (array $except = []) => $applyFilters(Product::query(), $except);

        $availabilityCounts = [
            'all' => (clone $filteredQuery(['availability']))->count(),
            'online' => (clone $filteredQuery(['availability']))->where('is_available_online', true)->count(),
            'offline' => (clone $filteredQuery(['availability']))->where('is_available_online', false)->count(),
        ];
        $imageCounts = [
            'all' => (clone $filteredQuery(['image']))->count(),
            'with' => (clone $filteredQuery(['image']))->where(function ($q) {
                $q->whereNotNull('image')->where('image', '!=', '')->orWhereHas('images');
            })->count(),
            'without' => (clone $filteredQuery(['image']))->where(function ($q) {
                $q->whereNull('image')->orWhere('image', '');
            })->whereDoesntHave('images')->count(),
        ];

        $products = $applyFilters(Product::with(['category', 'brand', 'unit', 'images']))
            ->orderBy('id')
            ->get();

        $categories = Category::orderBy('name')->get();
        $categoryCounts = $applyFilters(Product::query(), ['category'])
            ->selectRaw('category_id, count(*) as aggregate')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id');

        $categoryImagePaths = [];
        $representativeImages = ProductImage::query()
            ->join('products', 'products.id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', true)
            ->whereNotNull('products.category_id')
            ->orderBy('products.id')
            ->orderBy('product_images.order')
            ->orderBy('product_images.id')
            ->get(['products.category_id as category_id', 'product_images.image_path as image_path']);
        foreach ($representativeImages as $representativeImage) {
            $categoryImagePaths[$representativeImage->category_id] ??= $representativeImage->image_path;
        }

        $fallbackImages = Product::query()
            ->whereNotNull('category_id')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->orderBy('id')
            ->pluck('image', 'category_id');
        foreach ($fallbackImages as $categoryId => $imagePath) {
            $categoryImagePaths[$categoryId] ??= $imagePath;
        }

        $offlineCount = $totalCount - $onlineCount;
        return view('online.catalog', compact(
            'products',
            'categories',
            'categoryCounts',
            'categoryImagePaths',
            'selectedCategory',
            'categoryFilter',
            'availability',
            'availabilityCounts',
            'imageFilter',
            'imageCounts',
            'search',
            'totalCount',
            'onlineCount',
            'offlineCount'
        ));
    }

    private function resolveCatalogCategory(string $categoryFilter): ?Category
    {
        if ($categoryFilter === '') {
            return null;
        }

        return Category::query()
            ->when(is_numeric($categoryFilter), fn ($query) => $query->where('id', $categoryFilter), fn ($query) => $query->where('slug', $categoryFilter))
            ->first();
    }

    private function applyCatalogFilters($query, string $search, ?Category $selectedCategory, string $availability, string $imageFilter, array $except = [])
    {
        if ($search !== '' && ! in_array('search', $except, true)) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('barcode', 'like', $like)
                    ->orWhere('selling_price', 'like', $like)
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like))
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like));
            });
        }

        if ($selectedCategory && ! in_array('category', $except, true)) {
            $query->where('category_id', $selectedCategory->id);
        }

        if ($availability !== 'all' && ! in_array('availability', $except, true)) {
            $query->where('is_available_online', $availability === 'online');
        }

        if ($imageFilter !== 'all' && ! in_array('image', $except, true)) {
            if ($imageFilter === 'with') {
                $query->where(function ($q) {
                    $q->whereNotNull('image')->where('image', '!=', '')->orWhereHas('images');
                });
            } else {
                $query->where(function ($q) {
                    $q->whereNull('image')->orWhere('image', '');
                })->whereDoesntHave('images');
            }
        }

        return $query;
    }

    public function show(string $product)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $product->load(['category', 'brand', 'unit', 'images', 'onlineOrderItems.order']);
        // Tolerate missing columns when the color/variant migration hasn't run yet.
        $colorOptions = Schema::hasColumn('products', 'color')
            ? Product::whereNotNull('color')->where('color', '!=', '')->distinct()->orderBy('color')->limit(50)->pluck('color')
            : collect();
        $variantOptions = Schema::hasColumn('products', 'variant')
            ? Product::whereNotNull('variant')->where('variant', '!=', '')->distinct()->orderBy('variant')->limit(50)->pluck('variant')
            : collect();
        return view('online.catalog-show', compact('product', 'colorOptions', 'variantOptions'));
    }

    public function toggleOnlineStatus(Request $request, string $product)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $product->update(['is_available_online' => !$product->is_available_online]);
        return back()->with('success', 'Product online status updated!');
    }

    public function updateDetails(Request $request, string $product)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $validated = $request->validate([
            'color' => 'nullable|string|max:100',
            'variant' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'specifications' => 'nullable|string|max:5000',
        ]);

        $product->update([
            'description' => $validated['description'] ?? null,
            'specifications' => $validated['specifications'] ?? null,
            // Only when the color/variant migration has run; otherwise skip silently.
            ...(Schema::hasColumn('products', 'color') ? ['color' => $validated['color'] ?? null] : []),
            ...(Schema::hasColumn('products', 'variant') ? ['variant' => $validated['variant'] ?? null] : []),
        ]);

        $notice = 'Product details updated successfully!';
        if (! Schema::hasColumn('products', 'color')) {
            $notice .= ' (Note: color/variety columns not migrated yet — run php artisan migrate.)';
        }

        return back()->with('success', $notice);
    }

    public function bulkToggleOnlineStatus(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'availability' => ['nullable', 'in:all,online,offline'],
            'image' => ['nullable', 'in:all,with,without'],
        ]);

        $makeAvailable = $validated['action'] === 'activate';
        $ids = $validated['product_ids'] ?? [];
        $search = trim((string) ($validated['search'] ?? ''));
        $categoryFilter = trim((string) ($validated['category'] ?? ''));
        $availability = $validated['availability'] ?? 'all';
        $imageFilter = $validated['image'] ?? 'all';
        $selectedCategory = $this->resolveCatalogCategory($categoryFilter);

        if (! empty($ids)) {
            $count = Product::whereIn('id', $ids)->update(['is_available_online' => $makeAvailable, 'updated_at' => now()]);
            $scope = 'selected';
        } else {
            // No selection = apply to the current filters (or ALL products when no filter).
            $query = $this->applyCatalogFilters(Product::query(), $search, $selectedCategory, $availability, $imageFilter);
            $count = $query->update(['is_available_online' => $makeAvailable, 'updated_at' => now()]);
            $scope = ($search !== '' || $categoryFilter !== '' || $availability !== 'all' || $imageFilter !== 'all')
                ? 'matching current filters'
                : 'all';
        }

        $label = $makeAvailable ? 'activated (Online)' : 'deactivated (Offline)';
        $redirectParams = array_filter([
            'search' => $search !== '' ? $search : null,
            'category' => $categoryFilter !== '' ? $categoryFilter : null,
            'availability' => $availability !== 'all' ? $availability : null,
            'image' => $imageFilter !== 'all' ? $imageFilter : null,
        ]);

        return redirect()->route('online.catalog', $redirectParams)->with('success', "{$count} {$scope} product(s) {$label} successfully!");
    }

    public function uploadImage(Request $request, string $product)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:10240',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,gif,webp|max:10240',
        ]);

        $files = [];
        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images') as $file) {
                if ($file) {
                    $files[] = $file;
                }
            }
        }
        if ($request->hasFile('image')) {
            $files[] = $request->file('image');
        }

        if (empty($files)) {
            return back()->withErrors(['image' => 'Select at least one image or take a photo.']);
        }

        $order = $product->images()->count();
        $saved = 0;
        foreach ($files as $file) {
            if (! $file->isValid()) {
                continue;
            }
            $path = $this->storeAsWebp($file);
            if (! $path) {
                continue;
            }
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $order === 0 && $saved === 0,
                'order' => $order++,
            ]);
            $saved++;
        }

        if ($saved === 0) {
            return back()->withErrors(['image' => 'None of the images could be processed.']);
        }

        return back()->with('success', $saved > 1 ? "{$saved} images uploaded successfully!" : 'Image uploaded successfully!');
    }

    /**
     * Resize (max 1600px) and store an uploaded image as WebP.
     * Falls back to plain storage when GD is unavailable or conversion fails.
     */
    protected function storeAsWebp(\Illuminate\Http\UploadedFile $file): ?string
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $disk->makeDirectory('products');
        $filename = 'products/' . \Illuminate\Support\Str::random(40) . '.webp';

        try {
            $realPath = $file->getRealPath();
            $info = @getimagesize($realPath);
            $mime = $info['mime'] ?? $file->getMimeType();

            $src = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($realPath),
                'image/png' => @imagecreatefrompng($realPath),
                'image/gif' => @imagecreatefromgif($realPath),
                'image/webp' => @imagecreatefromwebp($realPath),
                default => false,
            };

            if ($src) {
                $width = imagesx($src);
                $height = imagesy($src);
                $maxSide = 1600;
                $scale = min(1, $maxSide / max($width, $height));
                $newWidth = (int) round($width * $scale);
                $newHeight = (int) round($height * $scale);

                $dst = imagecreatetruecolor($newWidth, $newHeight);
                // Preserve transparency (PNG/GIF/WebP)
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                $saved = @imagewebp($dst, $disk->path($filename), 82);
                imagedestroy($dst);
                imagedestroy($src);

                if ($saved) {
                    return $filename;
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('WebP conversion failed, storing original: ' . $e->getMessage());
        }

        // Fallback: store the original file untouched.
        try {
            return $file->store('products', 'public');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function deleteImage(Request $request, string $product, string $image)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $image = ProductImage::findByEncryptedKeyOrFail($image);
        abort_if($image->product_id !== $product->id, 404);
        $image->delete();
        return back()->with('success', 'Image deleted successfully!');
    }

    public function setPrimaryImage(Request $request, string $product, string $image)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $image = ProductImage::findByEncryptedKeyOrFail($image);
        abort_if($image->product_id !== $product->id, 404);
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return back()->with('success', 'Primary image set successfully!');
    }
}
