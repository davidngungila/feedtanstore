@include('reports._pdf-chrome', ['title' => 'INVENTORY INVESTMENT REPORT', 'period' => null])

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Categories</div>
        <div class="stat-value">{{ $categories->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Investment</div>
        <div class="stat-value">TZS {{ number_format($totalInvestment, 2) }}</div>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th>Category</th>
            <th style="text-align: right;">Investment Value</th>
            <th style="text-align: right;">Share %</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td style="text-align: right; font-weight: bold;">TZS {{ number_format($category->investment_value, 2) }}</td>
            <td style="text-align: right;">{{ $totalInvestment > 0 ? round(($category->investment_value / $totalInvestment) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

@include('reports._pdf-foot')
