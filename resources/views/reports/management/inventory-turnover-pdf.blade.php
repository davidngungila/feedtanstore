@include('reports._pdf-chrome', ['title' => 'INVENTORY TURNOVER REPORT', 'period' => $startDate . ' to ' . $endDate])

<table class="items-table">
    <thead>
        <tr>
            <th>Category</th>
            <th style="text-align: right;">COGS</th>
            <th style="text-align: right;">Avg Inventory Value</th>
            <th style="text-align: right;">Turnover Ratio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        @php
            $avgInventory = \App\Models\Product::where('category_id', $category->id)->sum(DB::raw('quantity * cost_price'));
        @endphp
        <tr>
            <td>{{ $category->name }}</td>
            <td style="text-align: right;">TZS {{ number_format($category->cogs, 2) }}</td>
            <td style="text-align: right;">TZS {{ number_format($avgInventory, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($category->turnover, 2) }}x</td>
        </tr>
        @endforeach
    </tbody>
</table>

@include('reports._pdf-foot')
