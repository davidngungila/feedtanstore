@include('reports._pdf-chrome', ['title' => 'HOURLY SALES REPORT', 'period' => $date])

@if($sales->isEmpty())
<div class="empty">No sales recorded on this date.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Hour</th>
            <th style="text-align: right;">Transactions</th>
            <th style="text-align: right;">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $row)
        <tr>
            <td>{{ str_pad($row->hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($row->hour, 2, '0', STR_PAD_LEFT) }}:59</td>
            <td style="text-align: right;">{{ number_format($row->count) }}</td>
            <td style="text-align: right;">TZS {{ number_format($row->total, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td>Total</td>
            <td style="text-align: right;">{{ number_format($sales->sum('count')) }}</td>
            <td style="text-align: right;">TZS {{ number_format($sales->sum('total'), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
