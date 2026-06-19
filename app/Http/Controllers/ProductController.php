<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $products = Product::with(['category', 'brand', 'unit'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%')
                      ->orWhere('barcode', 'like', '%' . $search . '%')
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('brand', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->get();
        
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')->count();
        
        return view('inventory.products', compact('products', 'search', 'lowStockCount'));
    }

    public function show(Product $product)
    {
        $product->load([
            'grnItems.goodsReceivedNote.supplier',
            'category',
            'brand',
            'unit',
            'saleItems.sale.customer'
        ]);
        
        $barcodeValue = $product->barcode ?? $product->sku ?? $product->id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($barcodeValue, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodePng);
        
        return view('inventory.products-show', compact('product', 'barcodeBase64', 'barcodeValue'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        return view('inventory.products-create', compact('categories', 'brands', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available_online' => 'boolean'
        ]);

        Product::create($request->all() + ['is_available_online' => $request->has('is_available_online')]);

        return redirect()->route('inventory.products')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        return view('inventory.products-edit', compact('product', 'categories', 'brands', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available_online' => 'boolean'
        ]);

        $product->update($request->all() + ['is_available_online' => $request->has('is_available_online')]);

        return redirect()->route('inventory.products')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('inventory.products')->with('success', 'Product deleted successfully!');
    }

    public function lowStock()
    {
        $products = Product::with(['category', 'brand', 'unit'])
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->get();
        return view('inventory.low-stock', compact('products'));
    }

    public function expiry()
    {
        $products = Product::with(['category', 'brand', 'unit'])
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->get();
        return view('inventory.expiry', compact('products'));
    }

    public function reports()
    {
        $totalProducts = Product::count();
        $totalValue = Product::sum(\DB::raw('quantity * cost_price'));
        $totalSellValue = Product::sum(\DB::raw('quantity * selling_price'));
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')->count();
        $outOfStockCount = Product::where('quantity', 0)->count();
        
        return view('inventory.reports', compact(
            'totalProducts', 
            'totalValue', 
            'totalSellValue', 
            'lowStockCount', 
            'outOfStockCount'
        ));
    }

    public function barcodes(Request $request)
    {
        $search = $request->search;
        $products = Product::with(['category', 'brand', 'unit'])
            ->where('is_active', true)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->get();
        return view('inventory.products-barcodes', compact('products', 'search'));
    }

    public function printBarcodes(Request $request)
    {
        $productIds = $request->product_ids;
        $quantities = $request->quantities ?? [];

        // Handle JSON string case (from expiry page)
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true);
        }

        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $products = Product::with(['category', 'brand', 'unit'])
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get();

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodes = [];
        foreach ($products as $product) {
            $qty = $quantities[$product->id] ?? 1;
            $qty = max(1, intval($qty));
            $barcodeValue = $product->barcode ?? $product->sku ?? $product->id;
            $barcodePng = $generator->getBarcode($barcodeValue, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128);
            
            for ($i = 0; $i < $qty; $i++) {
                $barcodes[] = [
                    'product' => $product,
                    'barcode_base64' => 'data:image/png;base64,' . base64_encode($barcodePng),
                    'barcode_value' => $barcodeValue
                ];
            }
        }

        return view('inventory.products-barcodes-print', compact('barcodes'));
    }

    public function printAllBarcodes()
    {
        $products = Product::with(['category', 'brand', 'unit'])
            ->where('is_active', true)
            ->get();

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodes = [];
        foreach ($products as $product) {
            $barcodeValue = $product->barcode ?? $product->sku ?? $product->id;
            $barcodePng = $generator->getBarcode($barcodeValue, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128);
            $barcodes[] = [
                'product' => $product,
                'barcode_base64' => 'data:image/png;base64,' . base64_encode($barcodePng),
                'barcode_value' => $barcodeValue
            ];
        }

        return view('inventory.products-barcodes-print', compact('barcodes'));
    }
}
