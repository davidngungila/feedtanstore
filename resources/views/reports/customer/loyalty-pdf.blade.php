@include('reports._pdf-chrome', ['title' => 'CUSTOMER LOYALTY REPORT', 'period' => null])

@if($customers->isEmpty())
<div class="empty">No loyalty points recorded yet.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Phone</th>
            <th style="text-align: right;">Points Earned</th>
            <th style="text-align: right;">Points Redeemed</th>
            <th style="text-align: right;">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->phone ?? '-' }}</td>
            <td style="text-align: right;">+{{ number_format($customer->points_earned) }}</td>
            <td style="text-align: right;">-{{ number_format($customer->points_redeemed) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($customer->loyalty_balance) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
