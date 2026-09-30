<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'unit', 'images'])->get();
        $categories = Category::all();
        return view('online.catalog', compact('products', 'categories'));
    }

    public function show(string $product)
    {
        $product = Product::findByEncryptedKeyOrFail($product);
        $product->load(['category', 'brand', 'unit', 'images', 'onlineOrderItems.order']);
        $colorOptions = Product::whereNotNull('color')->where('color', '!=', '')->distinct()->orderBy('color')->limit(50)->pluck('color');
        $variantOptions = Product::whereNotNull('variant')->where('variant', '!=', '')->distinct()->orderBy('variant')->limit(50)->pluck('variant');
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
            'color' => $validated['color'] ?? null,
            'variant' => $validated['variant'] ?? null,
            'description' => $validated['description'] ?? null,
            'specifications' => $validated['specifications'] ?? null,
        ]);

        return back()->with('success', 'Product details updated successfully!');
    }

    public function bulkToggleOnlineStatus(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $makeAvailable = $validated['action'] === 'activate';
        $ids = $validated['product_ids'] ?? [];

        if (empty($ids)) {
            // No selection = apply to ALL products
            $count = Product::query()->update(['is_available_online' => $makeAvailable, 'updated_at' => now()]);
        } else {
            $count = Product::whereIn('id', $ids)->update(['is_available_online' => $makeAvailable, 'updated_at' => now()]);
        }

        $label = $makeAvailable ? 'activated (Online)' : 'deactivated (Offline)';

        return back()->with('success', "{$count} product(s) {$label} successfully!");
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
