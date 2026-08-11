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

        <a href="{{ route('admin.verifikasi.index', ['filter' => 'all']) }}" class="av-stat-card transition-transform duration-200 hover:scale-[1.03] cursor-pointer" wire:navigate>
            <div class="av-stat-icon av-stat-icon--blue">📄</div>
            <div class="av-stat-value">{{ number_format($totalPengajuan) }}</div>
            <div class="av-stat-label">Total Pengajuan</div>
        </a>

        <a href="{{ route('admin.verifikasi.index', ['filter' => 'pending']) }}" class="av-stat-card transition-transform duration-200 hover:scale-[1.03] cursor-pointer" wire:navigate>
            <div class="av-stat-icon av-stat-icon--amber">⏳</div>
            <div class="av-stat-value">{{ number_format($pendingReview) }}</div>
            <div class="av-stat-label">Menunggu Verifikasi</div>
        </a>

        <a href="{{ route('admin.verifikasi.index', ['filter' => 'approved_unpaid']) }}" class="av-stat-card transition-transform duration-200 hover:scale-[1.03] cursor-pointer" wire:navigate>
            <div class="av-stat-icon av-stat-icon--green">✅</div>
            <div class="av-stat-value">{{ number_format($approvedCount) }}</div>
            <div class="av-stat-label">Disetujui</div>
        </a>

        <a href="{{ route('admin.verifikasi.index', ['filter' => 'rejected']) }}" class="av-stat-card transition-transform duration-200 hover:scale-[1.03] cursor-pointer" wire:navigate>
            <div class="av-stat-icon av-stat-icon--red">✕</div>
            <div class="av-stat-value">{{ number_format($rejectedCount) }}</div>
            <div class="av-stat-label">Ditolak</div>
        </a>

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

        <a href="{{ route('admin.client.index') }}" class="av-stat-card transition-transform duration-200 hover:scale-[1.03] cursor-pointer" wire:navigate>
            <div class="av-stat-icon av-stat-icon--blue">👤</div>
            <div class="av-stat-value">{{ number_format($totalClients) }}</div>
            <div class="av-stat-label">Total Klien Terdaftar</div>
        </a>

    </div>

    {{-- ══════════════ CHART FILTER ══════════════ --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="text-sm font-medium text-secondary">📊 Periode Chart:</span>
        <select wire:model.live="chartMonth"
                class="av-select text-sm rounded-lg px-3 py-1.5 bg-[#1e293b] border border-white/10 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 transition [&>option]:bg-[#1e293b] [&>option]:text-white">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endfor
        </select>
        <select wire:model.live="chartYear"
                class="av-select text-sm rounded-lg px-3 py-1.5 bg-[#1e293b] border border-white/10 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 transition [&>option]:bg-[#1e293b] [&>option]:text-white">
            @for ($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <span class="ml-auto av-pill av-pill--blue text-xs">{{ $chartMonthName }}</span>
    </div>

    {{-- ══════════════ CHARTS ══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">

        {{-- Revenue trend (area) — 3/5 width --}}
        <div class="av-card lg:col-span-3 overflow-hidden relative" wire:key="rev-{{ $chartMonth }}-{{ $chartYear }}">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent pointer-events-none"></div>
            <div class="av-card-header relative z-10">
                <div>
                    <span class="av-card-title">Pendapatan Harian</span>
                    <p class="text-xs text-muted mt-0.5">Total pendapatan per hari — {{ $chartMonthName }}</p>
                </div>
                <span class="av-badge av-badge--blue text-xs">
                    Rp{{ number_format(collect($chartRevenue)->sum(), 0, ',', '.') }}
                </span>
            </div>
            <div class="av-card-body relative z-10">
                <div x-data="revenueAreaChart(@js($chartLabels), @js($chartRevenue))" x-init="init()" style="height: 280px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>

        {{-- Daily requests (bar) — 2/5 width --}}
        <div class="av-card lg:col-span-2 overflow-hidden relative" wire:key="req-{{ $chartMonth }}-{{ $chartYear }}">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none"></div>
            <div class="av-card-header relative z-10">
                <div>
                    <span class="av-card-title">Pengajuan Masuk</span>
                    <p class="text-xs text-muted mt-0.5">Jumlah pengajuan per hari</p>
                </div>
                <span class="av-badge av-badge--green text-xs">
                    {{ collect($chartRequests)->sum() }} total
                </span>
            </div>
            <div class="av-card-body relative z-10">
                <div x-data="requestsBarChart(@js($chartLabels), @js($chartRequests))" x-init="init()" style="height: 280px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Payment methods --}}
    @if ($paymentMethods->count())
        <div class="av-card mb-6 overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 to-transparent pointer-events-none"></div>
            <div class="av-card-header relative z-10">
                <div>
                    <span class="av-card-title">Metode Pembayaran</span>
                    <p class="text-xs text-muted mt-0.5">Distribusi metode pembayaran yang digunakan klien</p>
                </div>
            </div>
            <div class="av-card-body relative z-10">
                <div x-data="paymentBarChart(@js(array_keys($paymentMethods->toArray())), @js(array_values($paymentMethods->toArray())))" x-init="init()" wire:ignore style="height: 200px;">
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
            <a href="{{ route('admin.riwayat.index') }}" class="av-btn av-btn--ghost av-btn--sm" wire:navigate>
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
        document.addEventListener('alpine:init', () => {

            /* ── Shared helpers ─────────────────────────────── */
            const fontStack = "'Inter', 'Segoe UI', system-ui, sans-serif";
            const tooltipStyle = {
                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                titleFont: { family: fontStack, size: 13, weight: '600' },
                bodyFont:  { family: fontStack, size: 12 },
                padding: { top: 10, bottom: 10, left: 14, right: 14 },
                cornerRadius: 10,
                borderColor: 'rgba(99, 102, 241, 0.25)',
                borderWidth: 1,
                displayColors: false,
                caretSize: 6,
            };
            const gridColor = 'rgba(148, 163, 184, 0.07)';
            const tickColor = '#64748b';

            /* ── Revenue Area Chart ─────────────────────────── */
            Alpine.data('revenueAreaChart', (labels, data) => ({
                chart: null,
                init() {
                    const ctx = this.$refs.canvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
                    gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.08)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Pendapatan',
                                data,
                                borderColor: '#3b82f6',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointRadius: 0,
                                pointHitRadius: 20,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#3b82f6',
                                pointHoverBorderWidth: 3,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            animation: {
                                duration: 1200,
                                easing: 'easeInOutQuart',
                                delay(ctx) { return ctx.dataIndex * 30; },
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    ...tooltipStyle,
                                    callbacks: {
                                        title: (items) => 'Tanggal ' + items[0].label,
                                        label: (item) => 'Rp ' + new Intl.NumberFormat('id-ID').format(item.raw),
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: tickColor,
                                        font: { family: fontStack, size: 11 },
                                        maxRotation: 0,
                                        callback(val, i) {
                                            const lbl = this.getLabelForValue(val);
                                            return (lbl % 5 === 0 || lbl === 1) ? lbl : '';
                                        },
                                    },
                                    grid: { display: false },
                                    border: { display: false },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: tickColor,
                                        font: { family: fontStack, size: 11 },
                                        callback: (v) => {
                                            if (v >= 1_000_000) return 'Rp' + (v / 1_000_000).toFixed(1) + 'jt';
                                            if (v >= 1_000) return 'Rp' + (v / 1_000).toFixed(0) + 'rb';
                                            return 'Rp' + v;
                                        },
                                        maxTicksLimit: 6,
                                    },
                                    grid: { color: gridColor },
                                    border: { display: false, dash: [4, 4] },
                                },
                            },
                        },
                    });
                },
            }));

            /* ── Requests Bar Chart ─────────────────────────── */
            Alpine.data('requestsBarChart', (labels, data) => ({
                chart: null,
                init() {
                    const ctx = this.$refs.canvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.25)');

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Pengajuan',
                                data,
                                backgroundColor: gradient,
                                hoverBackgroundColor: '#10b981',
                                borderRadius: { topLeft: 6, topRight: 6 },
                                borderSkipped: false,
                                maxBarThickness: 14,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart',
                                delay(ctx) { return ctx.dataIndex * 20; },
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    ...tooltipStyle,
                                    callbacks: {
                                        title: (items) => 'Tanggal ' + items[0].label,
                                        label: (item) => item.raw + ' pengajuan',
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: tickColor,
                                        font: { family: fontStack, size: 10 },
                                        maxRotation: 0,
                                        callback(val, i) {
                                            const lbl = this.getLabelForValue(val);
                                            return (lbl % 5 === 0 || lbl === 1) ? lbl : '';
                                        },
                                    },
                                    grid: { display: false },
                                    border: { display: false },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: tickColor,
                                        font: { family: fontStack, size: 11 },
                                        stepSize: 1,
                                        maxTicksLimit: 6,
                                    },
                                    grid: { color: gridColor },
                                    border: { display: false },
                                },
                            },
                        },
                    });
                },
            }));

            /* ── Payment Methods Bar Chart ──────────────────── */
            Alpine.data('paymentBarChart', (labels, data) => ({
                chart: null,
                init() {
                    const palette = [
                        'rgba(99, 102, 241, 0.85)',
                        'rgba(139, 92, 246, 0.85)',
                        'rgba(236, 72, 153, 0.85)',
                        'rgba(14, 165, 233, 0.85)',
                        'rgba(20, 184, 166, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                    ];
                    const hoverPalette = [
                        '#6366f1', '#8b5cf6', '#ec4899', '#0ea5e9', '#14b8a6', '#f59e0b',
                    ];

                    this.chart = new Chart(this.$refs.canvas, {
                        type: 'bar',
                        data: {
                            labels: labels.map(l => l.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
                            datasets: [{
                                label: 'Transaksi',
                                data,
                                backgroundColor: data.map((_, i) => palette[i % palette.length]),
                                hoverBackgroundColor: data.map((_, i) => hoverPalette[i % hoverPalette.length]),
                                borderRadius: 8,
                                maxBarThickness: 50,
                                borderSkipped: false,
                            }],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1000,
                                easing: 'easeOutBack',
                                delay(ctx) { return ctx.dataIndex * 100; },
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    ...tooltipStyle,
                                    callbacks: {
                                        label: (item) => item.raw + ' transaksi',
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: tickColor,
                                        font: { family: fontStack, size: 11 },
                                        stepSize: 1,
                                    },
                                    grid: { color: gridColor },
                                    border: { display: false },
                                },
                                y: {
                                    ticks: {
                                        color: '#cbd5e1',
                                        font: { family: fontStack, size: 12, weight: '500' },
                                    },
                                    grid: { display: false },
                                    border: { display: false },
                                },
                            },
                        },
                    });
                },
            }));

        });
    </script>
@endonce

