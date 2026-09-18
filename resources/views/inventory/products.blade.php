@extends('layouts.app')

@section('page-title', 'Products')

@section('content')
@php
    $productsData = $products->getCollection()->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'sku' => $p->sku,
        'barcode' => $p->barcode,
        'barcode_linked_at' => $p->barcode_linked_at ? $p->barcode_linked_at->format('Y-m-d H:i:s') : null,
        'category' => $p->category->name ?? '-',
        'brand' => $p->brand->name ?? '-',
        'unit' => $p->unit->short_name ?? ($p->unit->name ?? '-'),
        'quantity' => $p->quantity,
        'reorder_level' => $p->reorder_level,
        'cost_price' => $p->cost_price,
        'selling_price' => $p->selling_price,
        'expiry_date' => $p->expiry_date ? $p->expiry_date->format('Y-m-d') : null,
        'is_active' => (bool) $p->is_active,
        'is_available_online' => (bool) ($p->is_available_online ?? false),
        'description' => $p->description,
        'specifications' => $p->specifications,
    ])->keyBy('id')->toArray();
@endphp
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Products</h2>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('inventory.products') }}" method="GET" class="w-full md:w-64">
                    <div class="relative">
                        @if($status)
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="flex gap-3 md:hidden">
                    <button type="button" onclick="document.getElementById('mobileActions').classList.toggle('hidden')" class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-ellipsis-vertical"></i>
                    </button>
                </div>
                <div class="flex gap-3 hidden md:flex">
                    <a href="{{ route('inventory.products.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </a>
                    <button type="button" onclick="openImportModal()" class="border border-primary-600 text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-file-import mr-2"></i>Import Sheet
                    </button>
                    <a href="{{ route('inventory.products.export', request()->query()) }}" class="border border-primary-600 text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-file-export mr-2"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>

        <div id="mobileActions" class="hidden w-full md:hidden mt-2 mb-2 p-3 bg-white border border-gray-200 rounded-xl shadow-lg flex flex-col gap-2">
                    <a href="{{ route('inventory.products.create') }}" class="w-full text-left px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors whitespace-nowrap text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </a>
                    <button type="button" onclick="openImportModal(); document.getElementById('mobileActions').classList.add('hidden')" class="w-full text-left px-4 py-2 border border-primary-600 text-primary-600 hover:bg-primary-50 rounded-lg font-medium transition-colors text-sm">
                        <i class="fas fa-file-import mr-2"></i>Import Sheet
                    </button>
                    <a href="{{ route('inventory.products.export', request()->query()) }}" class="w-full text-left px-4 py-2 border border-primary-600 text-primary-600 hover:bg-primary-50 rounded-lg font-medium transition-colors text-sm">
                        <i class="fas fa-file-export mr-2"></i>Export Excel
                    </a>
                </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($lowStockCount > 0)
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-300 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-yellow-800">Low Stock Alert</h4>
                        <p class="text-xs text-yellow-700">{{ $lowStockCount }} product(s) are running low and need reordering.</p>
                    </div>
                </div>
                <a href="{{ route('inventory.low-stock') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-eye"></i> View Details
                </a>
            </div>
        @endif

        <div class="inline-flex rounded-lg border border-gray-300 bg-gray-100 p-1 mb-4">
            <a href="{{ route('inventory.products', ['status' => null, 'search' => $search]) }}"
               class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ !$status ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-600 hover:text-primary-600' }}">
                All ({{ $linkedCount + $notLinkedCount }})
            </a>
            <a href="{{ route('inventory.products', ['status' => 'linked', 'search' => $search]) }}"
               class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $status === 'linked' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-600 hover:text-primary-600' }}">
                <i class="fas fa-barcode mr-1 text-green-600"></i>Linked ({{ $linkedCount }})
            </a>
            <a href="{{ route('inventory.products', ['status' => 'not-linked', 'search' => $search]) }}"
               class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $status === 'not-linked' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-600 hover:text-primary-600' }}">
                <i class="fas fa-barcode mr-1 text-red-600"></i>Not Linked ({{ $notLinkedCount }})
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">
                            <input type="checkbox" id="select-all-products" class="w-4 h-4 text-primary-600">
                        </th>
                        <th class="text-left">Name</th>
                        <th class="text-left">SKU</th>
<th class="text-left">Barcode</th>
                        <th class="text-left">Scanned</th>
                        <th class="text-left">Linked</th>
                        <th class="text-left">Category</th>
                        <th class="text-left">Brand</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Cost Price</th>
                        <th class="text-left">Selling Price</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
<tbody id="products-table-body">
                    @include('inventory.partials._product_rows', ['products' => $products])
                </tbody>
            </table>
        </div>

        <div class="mt-4 p-3" id="products-pagination">
            {{ $products->links() }}
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search"]');
            const tableBody = document.getElementById('products-table-body');
            const selectAllCheckbox = document.getElementById('select-all-products');

            // Bind checkbox interactions (re-run after a search refresh)
            function bindProductChecks() {
                const checkboxes = tableBody.querySelectorAll('.product-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        updateBulkBar();
                        selectAllCheckbox.checked = Array.from(tableBody.querySelectorAll('.product-checkbox')).every(cb => cb.checked);
                    });
                });
                selectAllCheckbox.checked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
                selectAllCheckbox.onchange = function() {
                    tableBody.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
                    updateBulkBar();
                };
            }

            // Bulk delete
            const bulkDeleteForm = document.getElementById('bulk-delete-form');
            const bulkBar = document.getElementById('bulkActions');

            function updateBulkBar() {
                const checked = Array.from(tableBody.querySelectorAll('.product-checkbox')).filter(cb => cb.checked && !cb.closest('tr').style.display);
                if (checked.length > 0) {
                    document.getElementById('bulkCount').textContent = checked.length + ' selected';
                    bulkBar.classList.remove('hidden');
                } else {
                    bulkBar.classList.add('hidden');
                }
            }

            window.bulkDelete = function() {
                const checked = Array.from(tableBody.querySelectorAll('.product-checkbox')).filter(cb => cb.checked).length;
                if (checked === 0) return;
                if (confirm('Delete ' + checked + ' selected product(s)? This will permanently remove them and all related details (prices, images, transactions, etc.).')) {
                    bulkDeleteForm.submit();
                }
            };

// Live search - filters the full catalog on typing (AJAX, all pages)
            let searchTimer;
            searchInput.addEventListener('input', function() {
                updateBulkBar();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => performSearch(this.value.trim()), 150);
            });

            function performSearch(term) {
                const url = new URL('{{ route('inventory.products') }}');
                const status = url.searchParams.get('status') || null;
                if (status) url.searchParams.set('status', status);
                url.searchParams.set('search', term);
                history.replaceState(null, '', url);

                url.searchParams.set('fetch', '1');
                fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    tableBody.innerHTML = data.rows_html;
                    document.getElementById('products-pagination').innerHTML = data.pagination_html;
                    productsData = data.products_data;
                    bindProductChecks();
                    updateBulkBar();
                })
                .catch(() => {});
            }

            bindProductChecks();

            // Right drawer
            let productsData = @json($productsData);
            const drawer = document.getElementById('productDrawer');
            const drawerBackdrop = document.getElementById('productDrawerBackdrop');

            window.closeProductDrawer = function() {
                drawer.classList.add('translate-x-full');
                drawerBackdrop.classList.add('hidden');
                document.body.style.overflow = '';
            };

            function populateDrawer(data) {
                document.getElementById('drawerTitle').textContent = data.name;
                document.getElementById('drawerSku').textContent = data.sku || '-';
                document.getElementById('drawerBarcode').textContent = data.barcode || '-';
                document.getElementById('drawerCategory').textContent = data.category;
                document.getElementById('drawerBrand').textContent = data.brand;
                document.getElementById('drawerUnit').textContent = data.unit;
                document.getElementById('drawerQuantity').textContent = data.quantity;
                document.getElementById('drawerQuantity').className = 'font-semibold ' + (data.quantity <= data.reorder_level ? 'text-red-600' : 'text-primary-900');
                document.getElementById('drawerReorder').textContent = data.reorder_level;
                document.getElementById('drawerCost').textContent = 'TZS ' + Number(data.cost_price).toLocaleString();
                document.getElementById('drawerSelling').textContent = 'TZS ' + Number(data.selling_price).toLocaleString();
                document.getElementById('drawerExpiry').textContent = data.expiry_date ? new Date(data.expiry_date + 'T00:00:00').toLocaleDateString() : '-';

                const badges = document.getElementById('drawerBadges');
                badges.innerHTML = '';

                let badge = document.createElement('span');
                badge.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (data.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                badge.textContent = data.is_active ? 'Active' : 'Inactive';
                badges.appendChild(badge);

                badge = document.createElement('span');
                badge.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (data.barcode_linked_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800');
                badge.textContent = data.barcode_linked_at ? 'Scanned' : 'Not Scanned';
                badges.appendChild(badge);

                badge = document.createElement('span');
                badge.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (data.barcode ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                badge.textContent = data.barcode ? 'Linked' : 'Not Linked';
                badges.appendChild(badge);

                if (data.is_available_online) {
                    badge = document.createElement('span');
                    badge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800';
                    badge.textContent = 'Available Online';
                    badges.appendChild(badge);
                }

                if (data.quantity <= data.reorder_level) {
                    badge = document.createElement('span');
                    badge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800';
                    badge.textContent = 'Low Stock';
                    badges.appendChild(badge);
                }

                const descSection = document.getElementById('drawerDescription');
                if (data.description) {
                    descSection.classList.remove('hidden');
                    descSection.querySelector('p').textContent = data.description;
                } else {
                    descSection.classList.add('hidden');
                }

                const specSection = document.getElementById('drawerSpecifications');
                if (data.specifications) {
                    specSection.classList.remove('hidden');
                    specSection.querySelector('p').textContent = data.specifications;
                } else {
                    specSection.classList.add('hidden');
                }

                document.getElementById('drawerViewBtn').href = `/inventory/products/${data.id}`;
                document.getElementById('drawerEditBtn').href = `/inventory/products/${data.id}/edit`;
            }

            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('a, button, input, form')) return;
                const row = e.target.closest('tr[data-id]');
                if (row && productsData[row.dataset.id]) {
                    populateDrawer(productsData[row.dataset.id]);
                    drawerBackdrop.classList.remove('hidden');
                    requestAnimationFrame(() => drawer.classList.remove('translate-x-full'));
                    document.body.style.overflow = 'hidden';
                }
            });

            drawerBackdrop.addEventListener('click', closeProductDrawer);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !drawerBackdrop.classList.contains('hidden')) {
                    closeProductDrawer();
                }
            });
            });
        </script>
    </div>
</div>

<!-- Product Right Drawer -->
<form id="bulk-delete-form" action="{{ route('inventory.products.bulk-delete') }}" method="POST" class="hidden">
    @csrf
</form>

<div id="bulkActions" class="fixed bottom-6 right-6 z-30 hidden items-center gap-3 bg-white rounded-xl shadow-2xl border border-red-200 px-5 py-3">
    <span id="bulkCount" class="text-sm font-semibold text-gray-700"></span>
    <button type="button" onclick="bulkDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap">
        <i class="fas fa-trash"></i> Delete Selected
    </button>
</div>

<div id="productDrawerBackdrop" class="fixed inset-0 z-40 bg-black/40 hidden"></div>
<aside id="productDrawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out translate-x-full">
    <div class="flex items-center justify-between p-5 border-b">
        <h3 id="drawerTitle" class="text-lg font-bold text-primary-900 truncate">Product</h3>
        <button type="button" onclick="closeProductDrawer()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">
            &times;
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-6">
        <div id="drawerBadges" class="flex flex-wrap gap-2"></div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">SKU</p>
                <p id="drawerSku" class="font-medium text-gray-900 break-all">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Barcode</p>
                <p id="drawerBarcode" class="font-medium text-gray-900 break-all">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Category</p>
                <p id="drawerCategory" class="font-medium text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Brand</p>
                <p id="drawerBrand" class="font-medium text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Unit</p>
                <p id="drawerUnit" class="font-medium text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Quantity in Stock</p>
                <p id="drawerQuantity" class="font-semibold text-primary-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Reorder Level</p>
                <p id="drawerReorder" class="font-medium text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Expiry Date</p>
                <p id="drawerExpiry" class="font-medium text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Cost Price</p>
                <p id="drawerCost" class="font-medium text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Selling Price</p>
                <p id="drawerSelling" class="font-medium text-green-700">-</p>
            </div>
        </div>

        <div id="drawerDescription" class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
            <h4 class="font-semibold text-yellow-800 mb-1">Description</h4>
            <p class="text-sm text-yellow-700"></p>
        </div>

        <div id="drawerSpecifications" class="p-4 bg-gray-50 border-l-4 border-gray-300 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-1">Specifications</h4>
            <p class="text-sm text-gray-700"></p>
        </div>
    </div>

    <div class="p-5 border-t flex gap-3">
        <a id="drawerViewBtn" href="#" class="flex-1 text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
            <i class="fas fa-eye mr-2"></i>View Full Details
        </a>
        <a id="drawerEditBtn" href="#" class="flex-1 text-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
    </div>
</aside>

<!-- Import Sheet Modal -->
<div id="importModalBackdrop" class="fixed inset-0 z-40 bg-black/40 hidden"></div>
<div id="importModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                    <i class="fas fa-file-import"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-primary-900">Import Products from Sheet</h3>
                    <p class="text-xs text-gray-500">Upload .xlsx, .xls or .csv</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">
                &times;
            </button>
        </div>

        <form action="{{ route('inventory.products.import') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-5">
            @csrf
            <div>
                <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Choose file to import
                </label>
                <div id="importDropArea" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-primary-500 hover:bg-primary-50 transition-colors">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                    <p class="text-sm text-gray-600">Drag & drop your file here, or <span class="text-primary-600 font-semibold">browse</span></p>
                    <p id="importFileName" class="text-xs text-gray-500 mt-2 hidden"></p>
                    <input type="file" name="file" id="import_file" accept=".xlsx,.xls,.csv" class="hidden" required>
                </div>
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-600 space-y-1">
                <p class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-info-circle text-primary-600"></i>Instructions</p>
                <p>The first row must be the column headings.</p>
                <p>Required: <span class="font-medium">Product</span>. Optionally <span class="font-medium">Cartons</span> + <span class="font-medium">Quantity</span> (total = cartons × items per carton), <span class="font-medium">Price per carton (TZS)</span> (cost price), <span class="font-medium">Price per item</span> (selling price). The column <span class="font-medium">pcs</span> is also accepted.</p>
                <p>SKU, barcode, category, brand, unit and the standard field names are also accepted.</p>
                <p>Category, brand and unit are matched by name and auto-created if missing.</p>
                <p>Rows matching an existing SKU or barcode are updated instead of duplicated.</p>
                <p>Blank SKU is generated automatically; blank barcode is left empty so it can be scanned and linked later.</p>
            </div>

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('inventory.products.sample.download') }}" class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-800 font-medium">
                    <i class="fas fa-download"></i> Download sample sheet
                </a>
                <div class="flex gap-3">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-file-import"></i> Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const importModal = document.getElementById('importModal');
    const importModalBackdrop = document.getElementById('importModalBackdrop');
    const importDropArea = document.getElementById('importDropArea');
    const importFileInput = document.getElementById('import_file');
    const importFileName = document.getElementById('importFileName');

    window.openImportModal = function() {
        importModal.classList.remove('hidden');
        importModal.classList.add('flex');
        importModalBackdrop.classList.remove('hidden');
    };

    window.closeImportModal = function() {
        importModal.classList.add('hidden');
        importModal.classList.remove('flex');
        importModalBackdrop.classList.add('hidden');
    };

    importDropArea.addEventListener('click', () => importFileInput.click());
    importDropArea.addEventListener('dragover', (e) => { e.preventDefault(); importDropArea.classList.add('border-primary-600'); });
    importDropArea.addEventListener('dragleave', () => importDropArea.classList.remove('border-primary-600'));
    importDropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        importDropArea.classList.remove('border-primary-600');
        if (e.dataTransfer.files.length) {
            importFileInput.files = e.dataTransfer.files;
            showImportFileName();
        }
    });
    importFileInput.addEventListener('change', showImportFileName);

    function showImportFileName() {
        if (importFileInput.files.length) {
            importFileName.textContent = 'Selected: ' + importFileInput.files[0].name;
            importFileName.classList.remove('hidden');
        }
    }

    importModalBackdrop.addEventListener('click', closeImportModal);
    importFileInput.addEventListener('click', (e) => e.stopPropagation());
</script>
@endsection
