@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="tf-section-2 mb-30">
            <div class="flex gap20 flex-wrap-mobile">
                {{-- Kolom Kiri --}}
                <div class="w-half">
                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                                <div>
                                    <div class="body-text mb-2">Total Orders</div>
                                    <h4>{{ $totalOrders }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                                <div>
                                    <div class="body-text mb-2">Total Amount</div>
                                    <h4>Rp {{ number_format($totalAmount, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                                <div>
                                    <div class="body-text mb-2">Pending Orders</div>
                                    <h4>{{ $pendingOrdersCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                                <div>
                                    <div class="body-text mb-2">Pending Orders Amount</div>
                                    <h4>Rp {{ number_format($pendingOrdersAmount, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="w-half">
                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                                <div>
                                    <div class="body-text mb-2">Delivered Orders</div>
                                    <h4>{{ $deliveredOrdersCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                                <div>
                                    <div class="body-text mb-2">Delivered Orders Amount</div>
                                    <h4>Rp {{ number_format($deliveredOrdersAmount, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                                <div>
                                    <div class="body-text mb-2">Canceled Orders</div>
                                    <h4>{{ $canceledOrdersCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                                <div>
                                    <div class="body-text mb-2">Canceled Orders Amount</div>
                                    <h4>Rp {{ number_format($canceledOrdersAmount, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik Dinamis --}}
            <div class="wg-box mt-30">
                <div class="flex items-center justify-between mb-4">
                    <h5>Earnings & Orders Overview (Last 12 Months)</h5>
                    <div class="flex gap-2">
                        <button class="btn btn-primary chart-btn active" data-chart="revenue">Revenue</button>
                        <button class="btn btn-primary chart-btn" data-chart="orders">Orders</button>
                        <button class="btn btn-primary chart-btn" data-chart="status">Status</button>
                    </div>
                </div>

                <div id="chart-container">
                    <div id="chart-revenue" class="chart-box"></div>
                    <div id="chart-orders" class="chart-box hidden"></div>
                    <div id="chart-status" class="chart-box hidden"></div>
                </div>
            </div>
        </div>

        {{-- Tabel Pesanan Terbaru --}}
        <div class="tf-section mb-30">
            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Recent orders</h5>
                    <a class="btn btn-secondary" href="{{ route('admin.orders') }}">
                        <span class="view-all">View all</span>
                    </a>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>OrderNo</th>
                                    <th>Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="text-center">{{ $order->order_code }}</td>
                                    <td>{{ $order->name }}</td>
                                    <td class="text-center">{{ $order->phone }}</td>
                                    <td class="text-center">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge 
                                            @if($order->status == 'ordered') badge-warning 
                                            @elseif($order->status == 'delivered') badge-success 
                                            @elseif($order->status == 'canceled') badge-danger 
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="text-center">{{ $order->orderItems->count() }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.order.items', ['order_id' => $order->id]) }}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye"><i class="icon-eye"></i></div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No recent orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartLabels = @json($chartLabels);
    const chartRevenue = @json($chartRevenue);
    const chartOrders = @json($chartOrders);
    const chartPending = @json($chartPending);
    const chartDelivered = @json($chartDelivered);
    const chartCanceled = @json($chartCanceled);

    // ====== CHARTS ======
    const revenueChart = new ApexCharts(document.querySelector("#chart-revenue"), {
        series: [{ name: "Revenue", data: chartRevenue }],
        chart: { type: 'area', height: 300, toolbar: { show: false } },
        stroke: { curve: 'smooth' },
        xaxis: { categories: chartLabels },
        yaxis: { labels: { formatter: val => "Rp " + new Intl.NumberFormat('id-ID').format(val) }},
        title: { text: 'Monthly Revenue', align: 'left' },
        colors: ['#00b894']
    });
    revenueChart.render();

    const ordersChart = new ApexCharts(document.querySelector("#chart-orders"), {
        series: [{ name: "Total Orders", data: chartOrders }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        dataLabels: { enabled: true },
        xaxis: { categories: chartLabels },
        title: { text: 'Total Orders per Month', align: 'left' },
        colors: ['#0984e3']
    });
    ordersChart.render();

    const statusChart = new ApexCharts(document.querySelector("#chart-status"), {
        series: [
            { name: "Pending", data: chartPending },
            { name: "Delivered", data: chartDelivered },
            { name: "Canceled", data: chartCanceled }
        ],
        chart: { type: 'line', height: 320, toolbar: { show: false } },
        stroke: { curve: 'smooth' },
        xaxis: { categories: chartLabels },
        title: { text: 'Order Status Trends', align: 'left' },
        colors: ['#f9ca24', '#00b894', '#d63031']
    });
    statusChart.render();

    // ====== SWITCH CHART ======
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const target = btn.dataset.chart;
            document.querySelectorAll('.chart-box').forEach(box => box.classList.add('hidden'));
            document.querySelector(`#chart-${target}`).classList.remove('hidden');
        });
    });
});
</script>

<style>
.chart-btn.active {
    background-color: #0984e3 !important;
    color: #fff !important;
}
.chart-box.hidden {
    display: none;
}
.chart-box {
    transition: opacity 0.3s ease-in-out;
}
</style>
@endpush