@extends('layouts.app')

@section('page-title', 'Purchase Reports')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
  <div class="card rounded-2xl p-6">
    <h2 class="text-xl font-bold text-primary-900 mb-6">Purchase Reports</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">Total Orders</span>
          <i class="fa-solid fa-file-invoice text-2xl opacity-80"></i>
        </div>
        <p class="text-2xl font-bold">{{ $totalOrders }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">Total GRNs</span>
          <i class="fa-solid fa-box text-2xl opacity-80"></i>
        </div>
        <p class="text-2xl font-bold">{{ $totalGrns }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">Total Payments</span>
          <i class="fa-solid fa-credit-card text-2xl opacity-80"></i>
        </div>
        <p class="text-2xl font-bold">{{ $totalPayments }}</p>
      </div>
      <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">Total Amount</span>
          <i class="fa-solid fa-coins text-2xl opacity-80"></i>
        </div>
        <p class="text-2xl font-bold">TZS {{ number_format($totalAmount, 2) }}</p>
      </div>
      <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm opacity-90">Total Paid</span>
          <i class="fa-solid fa-money-check-dollar text-2xl opacity-80"></i>
        </div>
        <p class="text-2xl font-bold">TZS {{ number_format($totalPaid, 2) }}</p>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white border border-gray-100 rounded-xl p-5">
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Monthly Purchase Orders</h3>
        <canvas id="ordersChart"></canvas>
      </div>
      <div class="bg-white border border-gray-100 rounded-xl p-5">
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Monthly Payments</h3>
        <canvas id="paymentsChart"></canvas>
      </div>
    </div>

    <!-- Top Suppliers -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-primary-900 mb-4">Top Suppliers</h3>
      <div class="overflow-x-auto">
        <table class="data-table w-full">
          <thead>
            <tr>
              <th class="text-left">Supplier</th>
              <th class="text-left">Total Orders</th>
              <th class="text-left">Total GRNs</th>
              <th class="text-left">Total Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($topSuppliers as $supplier)
            <tr>
              <td class="font-medium text-primary-900">{{ $supplier->name }}</td>
              <td class="text-gray-600">{{ $supplier->purchase_orders_count }}</td>
              <td class="text-gray-600">{{ $supplier->goods_received_notes_count }}</td>
              <td class="text-gray-600 font-semibold">TZS {{ number_format($supplier->purchase_orders_sum_total, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div>
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Recent Purchase Orders</h3>
        <div class="overflow-x-auto">
          <table class="data-table w-full">
            <thead>
              <tr>
                <th class="text-left">PO Number</th>
                <th class="text-left">Supplier</th>
                <th class="text-left">Date</th>
                <th class="text-left">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentOrders as $order)
              <tr>
                <td class="font-medium text-primary-900">
                  <a href="{{ route('purchasing.orders.show', $order) }}" class="hover:underline">{{ $order->po_number }}</a>
                </td>
                <td class="text-gray-600">{{ $order->supplier->name ?? 'N/A' }}</td>
                <td class="text-gray-600">{{ $order->order_date ? date('M d, Y', strtotime($order->order_date)) : '-' }}</td>
                <td class="text-gray-600">TZS {{ number_format($order->total, 2) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Recent GRNs</h3>
        <div class="overflow-x-auto">
          <table class="data-table w-full">
            <thead>
              <tr>
                <th class="text-left">GRN Number</th>
                <th class="text-left">Supplier</th>
                <th class="text-left">Date</th>
                <th class="text-left">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentGrns as $grn)
              <tr>
                <td class="font-medium text-primary-900">
                  <a href="{{ route('purchasing.grn.show', $grn) }}" class="hover:underline">{{ $grn->grn_number }}</a>
                </td>
                <td class="text-gray-600">{{ $grn->supplier->name ?? 'N/A' }}</td>
                <td class="text-gray-600">{{ $grn->received_date ? date('M d, Y', strtotime($grn->received_date)) : '-' }}</td>
                <td class="text-gray-600">TZS {{ number_format($grn->total, 2) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Monthly Orders Chart
  const ordersCtx = document.getElementById('ordersChart').getContext('2d');
  const ordersData = {
    labels: {!! json_encode($monthlyOrders->map(function($item) { 
        return date('M Y', mktime(0, 0, 0, intval($item['month']), 1, intval($item['year']))); 
    })) !!},
    datasets: [
      {
        label: 'Total Amount',
        data: {!! json_encode($monthlyOrders->pluck('total')) !!},
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.3,
        fill: true,
        yAxisID: 'y',
      },
      {
        label: 'Number of Orders',
        data: {!! json_encode($monthlyOrders->pluck('count')) !!},
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.3,
        fill: true,
        yAxisID: 'y1',
      }
    ]
  };

  new Chart(ordersCtx, {
    type: 'line',
    data: ordersData,
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      scales: {
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          title: {
            display: true,
            text: 'Amount (TZS)'
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Count'
          },
          grid: {
            drawOnChartArea: false,
          },
        },
      }
    }
  });

  // Monthly Payments Chart
  const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
  const paymentsData = {
    labels: {!! json_encode($monthlyPayments->map(function($item) { 
        return date('M Y', mktime(0, 0, 0, intval($item['month']), 1, intval($item['year']))); 
    })) !!},
    datasets: [
      {
        label: 'Total Paid',
        data: {!! json_encode($monthlyPayments->pluck('total')) !!},
        borderColor: '#8b5cf6',
        backgroundColor: 'rgba(139, 92, 246, 0.1)',
        tension: 0.3,
        fill: true,
        yAxisID: 'y',
      },
      {
        label: 'Number of Payments',
        data: {!! json_encode($monthlyPayments->pluck('count')) !!},
        borderColor: '#f59e0b',
        backgroundColor: 'rgba(245, 158, 11, 0.1)',
        tension: 0.3,
        fill: true,
        yAxisID: 'y1',
      }
    ]
  };

  new Chart(paymentsCtx, {
    type: 'line',
    data: paymentsData,
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      scales: {
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          title: {
            display: true,
            text: 'Amount (TZS)'
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Count'
          },
          grid: {
            drawOnChartArea: false,
          },
        },
      }
    }
  });
</script>
@endsection
