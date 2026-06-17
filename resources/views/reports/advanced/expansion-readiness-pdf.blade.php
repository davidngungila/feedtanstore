<!DOCTYPE html>
<html>
<head>
    <title>Expansion Readiness Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .score-card { border: 2px solid #3b82f6; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 20px; background-color: #eff6ff; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .stat-card { border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; }
        .text-green { color: #16a34a; }
        .text-orange { color: #ea580c; }
        .text-purple { color: #9333ea; }
        .text-blue { color: #2563eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Expansion Readiness Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="score-card">
        @php
            $score = 0;
            $score += $salesGrowth['growth'] > 5 ? 25 : ($salesGrowth['growth'] > 0 ? 15 : 5);
            $score += $inventoryTurnover > 3 ? 25 : ($inventoryTurnover > 1 ? 15 : 5);
            $score += $customerRetention > 50 ? 25 : ($customerRetention > 20 ? 15 : 5);
            $score += $cashFlowHealth['net'] > 0 ? 25 : ($cashFlowHealth['net'] > -1000 ? 15 : 5);
        @endphp
        <h3>Overall Readiness Score: {{ $score }}/100</h3>
        <p>{{ $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : ($score >= 40 ? 'Fair' : 'Needs Improvement')) }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <p class="text-green">Sales Growth</p>
            <h3>{{ number_format($salesGrowth['growth'], 2) }}%</h3>
            <p>First Half: TZS {{ number_format($salesGrowth['first_half'], 0) }}</p>
            <p>Second Half: TZS {{ number_format($salesGrowth['second_half'], 0) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-orange">Inventory Turnover</p>
            <h3>{{ number_format($inventoryTurnover, 2) }}x</h3>
            <p>Times per period</p>
        </div>
        <div class="stat-card">
            <p class="text-purple">Customer Retention</p>
            <h3>{{ number_format($customerRetention, 2) }}%</h3>
            <p>Repeat customers</p>
        </div>
        <div class="stat-card">
            <p class="text-blue">Net Cash Flow</p>
            <h3 class="{{ $cashFlowHealth['net'] >= 0 ? 'text-green' : 'text-red' }}">TZS {{ number_format($cashFlowHealth['net'], 2) }}</h3>
            <p>Sales: TZS {{ number_format($cashFlowHealth['sales'], 0) }}</p>
            <p>Expenses: TZS {{ number_format($cashFlowHealth['expenses'], 0) }}</p>
        </div>
    </div>
</body>
</html>