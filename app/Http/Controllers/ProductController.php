<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Imports\ProductImport;
use App\Exports\ProductSampleExport;
use App\Exports\ProductExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    protected function generateUniqueSku(?string $name = null): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '', $name ?? 'PRD') ?: 'PRD', 0, 4));

        do {
            $sku = $base . '-' . now()->format('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

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
            ->when($status === 'linked', function ($query) {
                $query->whereNotNull('barcode');
            })
            ->when($status === 'not-linked', function ($query) {
                $query->whereNull('barcode');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $linkedCount = Product::whereNotNull('barcode')->count();
        $notLinkedCount = Product::whereNull('barcode')->count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')->count();

        return view('inventory.products', compact('products', 'search', 'status', 'linkedCount', 'notLinkedCount', 'lowStockCount'));
    }

    public function show($identifier)
    {
        $product = Product::where('id', $identifier)
            ->orWhere('sku', $identifier)
            ->orWhere('barcode', $identifier)
            ->firstOrFail();
            
        $product->load([
            'grnItems.goodsReceivedNote.supplier',
            'category',
            'brand',
            'unit',
            'saleItems.sale.customer'
        ]);
        
        $barcodeValue = $product->barcode;
        $barcodeBase64 = null;
        if ($barcodeValue) {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodePng = $generator->getBarcode($barcodeValue, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128);
            $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodePng);
        }
        
        return view('inventory.products-show', compact('product', 'barcodeBase64', 'barcodeValue'));
    }

    public function checkBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        if (!$barcode) {
            return response()->json(['exists' => false]);
        }

        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json([
                'exists' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function linkBarcode(Request $request, $identifier)
    {
        $product = Product::where('id', $identifier)
            ->orWhere('sku', $identifier)
            ->orWhere('barcode', $identifier)
            ->firstOrFail();

        $request->validate([
            'barcode' => 'required|string|max:255|unique:products,barcode,' . $product->id,
            'expiry_date' => 'nullable|date',
        ]);

        $product->update([
            'barcode' => $request->barcode,
            'barcode_linked_at' => now(),
            'expiry_date' => $request->filled('expiry_date') ? $request->expiry_date : $product->expiry_date,
        ]);

        return response()->json([
            'success' => true,
            'barcode' => $product->barcode,
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        $generatedSku = $this->generateUniqueSku();

        return view('inventory.products-create', compact('categories', 'brands', 'units', 'generatedSku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
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

        $payload = $request->all();
        $payload['sku'] = $request->filled('sku') ? $request->sku : $this->generateUniqueSku($request->name);
        $payload['barcode'] = $request->filled('barcode') ? $request->barcode : null;
        $payload['barcode_linked_at'] = $request->filled('barcode') ? now() : null;
        $payload['is_available_online'] = $request->has('is_available_online');

        Product::create($payload);

        return redirect()->route('inventory.products')->with('success', 'Product created successfully!');
    }

    public function edit($identifier)
    {
        $product = Product::where('id', $identifier)
            ->orWhere('sku', $identifier)
            ->orWhere('barcode', $identifier)
            ->firstOrFail();
            
        $categories = Category::all();
        $brands = Brand::all();
        $units = Unit::all();
        return view('inventory.products-edit', compact('product', 'categories', 'brands', 'units'));
    }

    public function update(Request $request, $identifier)
    {
        $product = Product::where('id', $identifier)
            ->orWhere('sku', $identifier)
            ->orWhere('barcode', $identifier)
            ->firstOrFail();
            
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
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

        $product->update($request->except('barcode', 'barcode_linked_at') + ['barcode' => $request->filled('barcode') ? $request->barcode : null, 'barcode_linked_at' => $request->filled('barcode') ? ($product->barcode === $request->barcode ? $product->barcode_linked_at : now()) : null] + ['is_available_online' => $request->has('is_available_online')]);

        return redirect()->route('inventory.products')->with('success', 'Product updated successfully!');
    }

    public function destroy($identifier)
    {
        $product = Product::where('id', $identifier)
            ->orWhere('sku', $identifier)
            ->orWhere('barcode', $identifier)
            ->firstOrFail();
            
        $product->delete();
        return redirect()->route('inventory.products')->with('success', 'Product deleted successfully!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $count = Product::whereIn('id', $request->product_ids)->delete();

        return redirect()->route('inventory.products')->with('success', $count . ' product(s) deleted successfully!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new ProductImport();
            Excel::import($import, $request->file('file'));
            $count = $import->getImportedCount();
            $updated = $import->getUpdatedCount();
            $failures = $import->getFailures();

            $message = "Successfully imported {$count} product(s)";
            if ($updated > 0) {
                $message .= " and updated {$updated} existing product(s)";
            }
            $message .= "!";

            if (!empty($failures)) {
                $message .= " However, " . count($failures) . " row(s) failed: " . implode("; ", array_slice($failures, 0, 20));
                if (count($failures) > 20) {
                    $message .= "...";
                }
                return redirect()->route('inventory.products')->with('warning', $message);
            }

            return redirect()->route('inventory.products')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('inventory.products')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        return Excel::download(new ProductSampleExport(), 'products_sample.xlsx');
    }

    public function export(Request $request)
    {
        return Excel::download(new ProductExport($request->input('search'), $request->input('status')), 'products_' . now()->format('Ymd_His') . '.xlsx');
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
}
