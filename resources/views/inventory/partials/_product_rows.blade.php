@foreach($products as $product)
                    <tr data-search="{{ strtolower($product->name . ' ' . ($product->sku ?? '') . ' ' . ($product->barcode ?? '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->brand->name ?? '')) }}" data-id="{{ $product->id }}" class="cursor-pointer hover:bg-gray-50 transition-colors">
                        <td class="text-left">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulk-delete-form" class="product-checkbox w-4 h-4 text-primary-600">
                        </td>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('inventory.products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                        </td>
                        <td class="text-gray-600">{{ $product->sku ?? '-' }}</td>
                        <td class="text-gray-600">{{ $product->barcode ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $product->barcode_linked_at ? 'badge-green' : 'badge-yellow' }}">
                                {{ $product->barcode_linked_at ? 'Scanned' : 'Not Scanned' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $product->barcode ? 'badge-green' : 'badge-red' }}">
                                {{ $product->barcode ? 'Linked' : 'Not Linked' }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="text-gray-600">{{ $product->brand->name ?? '-' }}</td>
                        <td class="font-semibold {{ $product->quantity <= $product->reorder_level ? 'text-red-600' : 'text-primary-900' }}">
                            {{ $product->quantity }} {{ $product->unit->short_name ?? '' }}
                        </td>
                        <td class="text-gray-600">TZS {{ number_format($product->cost_price, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($product->selling_price, 2) }}</td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('inventory.products.show', $product) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventory.products.edit', $product) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('inventory.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach