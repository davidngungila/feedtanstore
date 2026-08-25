@include('reports._pdf-chrome', ['title' => 'CUSTOMER SALES REPORT', 'period' => $startDate . ' to ' . $endDate])

@if($customers->isEmpty())
<div class="empty">No customers found.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Phone</th>
            <th style="text-align: right;">Purchases</th>
            <th style="text-align: right;">Total Spent</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->phone ?? '-' }}</td>
            <td style="text-align: right;">{{ number_format($customer->purchase_count) }}</td>
            <td style="text-align: right;">TZS {{ number_format($customer->total_spent, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td colspan="2" style="text-align: right;">Total</td>
            <td style="text-align: right;">{{ number_format($customers->sum('purchase_count')) }}</td>
            <td style="text-align: right;">TZS {{ number_format($customers->sum('total_spent'), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
