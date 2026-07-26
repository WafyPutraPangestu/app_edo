@once
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endonce

<div class="av-content" x-data="{ activeTab: 'requests' }">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1>Dashboard Admin</h1>
            <p class="text-muted text-sm mt-1">Ringkasan operasional & keuangan PT. NCS Line World Wide</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="av-pill av-pill--blue">
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- ══════════════ STAT CARDS ══════════════ --}}
    <div class="av-grid-stats mb-6">

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--blue">📄</div>
            <div class="av-stat-value">{{ number_format($totalPengajuan) }}</div>
            <div class="av-stat-label">Total Pengajuan</div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--amber">⏳</div>
            <div class="av-stat-value">{{ number_format($pendingReview) }}</div>
            <div class="av-stat-label">Menunggu Verifikasi</div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--green">✅</div>
            <div class="av-stat-value">{{ number_format($approvedCount) }}</div>
            <div class="av-stat-label">Disetujui</div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--red">✕</div>
            <div class="av-stat-value">{{ number_format($rejectedCount) }}</div>
            <div class="av-stat-label">Ditolak</div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--green">💰</div>
            <div class="av-stat-value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="av-stat-label">Total Pendapatan (Lunas)</div>
            <div class="av-stat-delta {{ $revenueGrowth >= 0 ? 'av-stat-delta--up' : 'av-stat-delta--down' }}">
                {{ $revenueGrowth >= 0 ? '▲' : '▼' }} {{ abs($revenueGrowth) }}% vs bulan lalu
            </div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--amber">🧾</div>
            <div class="av-stat-value">Rp{{ number_format($unpaidAmount, 0, ',', '.') }}</div>
            <div class="av-stat-label">Tagihan Belum Dibayar</div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--blue">👤</div>
            <div class="av-stat-value">{{ number_format($totalClients) }}</div>
            <div class="av-stat-label">Total Klien Terdaftar</div>
        </div>

    </div>

    {{-- ══════════════ CHARTS ══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Revenue trend --}}
        <div class="av-card lg:col-span-2">
            <div class="av-card-header">
                <span class="av-card-title">Tren Pendapatan (6 Bulan Terakhir)</span>
                <span class="av-badge av-badge--blue">Realtime</span>
            </div>
            <div class="av-card-body">
                <div x-data="revenueChart(@js($chartLabels), @js($chartRevenue))" x-init="init()" wire:ignore style="height: 260px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>

        {{-- Status breakdown --}}
        <div class="av-card">
            <div class="av-card-header">
                <span class="av-card-title">Status Pengajuan</span>
            </div>
            <div class="av-card-body">
                <div x-data="statusChart(@js(array_keys($statusBreakdown)), @js(array_values($statusBreakdown)))" x-init="init()" wire:ignore style="height: 220px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
                <div class="flex justify-center gap-4 mt-4 text-xs">
                    <span class="flex items-center gap-1.5 text-secondary">
                        <span class="inline-block w-2 h-2 rounded-full" style="background:#f59e0b"></span> Pending
                    </span>
                    <span class="flex items-center gap-1.5 text-secondary">
                        <span class="inline-block w-2 h-2 rounded-full" style="background:#10b981"></span> Approved
                    </span>
                    <span class="flex items-center gap-1.5 text-secondary">
                        <span class="inline-block w-2 h-2 rounded-full" style="background:#ef4444"></span> Rejected
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Payment methods bar chart --}}
    @if ($paymentMethods->count())
        <div class="av-card mb-6">
            <div class="av-card-header">
                <span class="av-card-title">Distribusi Metode Pembayaran</span>
            </div>
            <div class="av-card-body">
                <div x-data="paymentChart(@js(array_keys($paymentMethods->toArray())), @js(array_values($paymentMethods->toArray())))" x-init="init()" wire:ignore style="height: 200px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════ RECENT ACTIVITY (TABS) ══════════════ --}}
    <div class="av-card">
        <div class="av-card-header">
            <div class="flex items-center gap-1">
                <button @click="activeTab = 'requests'" class="av-navbar-link"
                    :class="activeTab === 'requests' && 'active'">
                    Pengajuan Terbaru
                </button>
                <button @click="activeTab = 'transactions'" class="av-navbar-link"
                    :class="activeTab === 'transactions' && 'active'">
                    Transaksi Terbaru
                </button>
            </div>
            <a href="#" class="av-btn av-btn--ghost av-btn--sm">
                Lihat Semua →
            </a>
        </div>

        {{-- Tab: Pengajuan --}}
        <div x-show="activeTab === 'requests'" x-cloak>
            <div class="overflow-x-auto">
                <table class="av-table">
                    <thead>
                        <tr>
                            <th>Klien</th>
                            <th>No. AWB</th>
                            <th>Rute</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $req)
                            <tr>
                                <td class="col-primary">{{ $req->user->name ?? '-' }}</td>
                                <td>{{ $req->awb_number ?? '—' }}</td>
                                <td>{{ $req->origin ?? '—' }} → {{ $req->destination ?? '—' }}</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'pending' => 'amber',
                                            'approved' => 'green',
                                            'rejected' => 'red',
                                        ];
                                    @endphp
                                    <span class="av-pill av-pill--{{ $statusMap[$req->status] ?? 'gray' }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $req->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="av-empty">
                                        <div class="av-empty-icon">📭</div>
                                        <div class="av-empty-title">Belum ada pengajuan</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Transaksi --}}
        <div x-show="activeTab === 'transactions'" x-cloak>
            <div class="overflow-x-auto">
                <table class="av-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Klien</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $trx)
                            <tr>
                                <td class="col-primary">{{ $trx->invoice_number }}</td>
                                <td>{{ $trx->user->name ?? '-' }}</td>
                                <td>Rp{{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $trxMap = [
                                            'unpaid' => 'gray',
                                            'paid' => 'green',
                                            'failed' => 'red',
                                        ];
                                    @endphp
                                    <span class="av-pill av-pill--{{ $trxMap[$trx->status] ?? 'gray' }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $trx->payment_date ? $trx->payment_date->diffForHumans() : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="av-empty">
                                        <div class="av-empty-icon">💳</div>
                                        <div class="av-empty-title">Belum ada transaksi</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ ALPINE CHART FACTORIES ══════════════ --}}
@once
    <script>
        function aeroChartDefaults() {
            return {
                color: '#94a3b8',
                grid: 'rgba(148, 163, 184, 0.08)',
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('revenueChart', (labels, data) => ({
                chart: null,
                init() {
                    const d = aeroChartDefaults();
                    this.chart = new Chart(this.$refs.canvas, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Pendapatan',
                                data: data,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointBackgroundColor: '#3b82f6',
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: d.color
                                    },
                                    grid: {
                                        color: 'transparent'
                                    }
                                },
                                y: {
                                    ticks: {
                                        color: d.color,
                                        callback: (v) => 'Rp' + (v / 1000000).toFixed(1) + 'jt',
                                    },
                                    grid: {
                                        color: d.grid
                                    },
                                },
                            },
                        },
                    });
                },
            }));

            Alpine.data('statusChart', (labels, data) => ({
                chart: null,
                init() {
                    this.chart = new Chart(this.$refs.canvas, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                                borderColor: '#111d35',
                                borderWidth: 3,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                        },
                    });
                },
            }));

            Alpine.data('paymentChart', (labels, data) => ({
                chart: null,
                init() {
                    const d = aeroChartDefaults();
                    this.chart = new Chart(this.$refs.canvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Transaksi',
                                data: data,
                                backgroundColor: '#3b82f6',
                                borderRadius: 6,
                                maxBarThickness: 40,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: d.color
                                    },
                                    grid: {
                                        color: 'transparent'
                                    }
                                },
                                y: {
                                    ticks: {
                                        color: d.color,
                                        stepSize: 1
                                    },
                                    grid: {
                                        color: d.grid
                                    }
                                },
                            },
                        },
                    });
                },
            }));
        });
    </script>
@endonce
