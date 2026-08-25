@include('reports._pdf-chrome', ['title' => 'BUSINESS GROWTH REPORT', 'period' => 'Last 12 months'])

<table class="items-table">
    <thead>
        <tr>
            <th>Month</th>
            <th style="text-align: right;">Sales</th>
            <th style="text-align: right;">Gross Profit</th>
            <th style="text-align: right;">Margin %</th>
            <th style="text-align: right;">Growth vs Previous</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salesData as $index => $row)
        @php
            $prev = $index > 0 ? $salesData[$index - 1]['sales'] : 0;
            $growth = $prev > 0 ? round((($row['sales'] - $prev) / $prev) * 100, 1) : null;
            $margin = $row['sales'] > 0 ? round(($row['profit'] / $row['sales']) * 100, 1) : 0;
        @endphp
        <tr>
            <td>{{ $row['month'] }}</td>
            <td style="text-align: right;">TZS {{ number_format($row['sales'], 2) }}</td>
            <td style="text-align: right;">TZS {{ number_format($row['profit'], 2) }}</td>
            <td style="text-align: right;">{{ $margin }}%</td>
            <td style="text-align: right; font-weight: bold;">
                {{ $growth === null ? '-' : (($growth >= 0 ? '+' : '') . $growth . '%') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@include('reports._pdf-foot')
