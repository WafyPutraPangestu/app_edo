<div>
    {{-- TOPBAR --}}
    <div class="av-topbar">
        <span class="av-page-title">Laporan & Rekap</span>
        <div class="av-topbar-actions">
            <button onclick="window.print()" class="av-btn av-btn--ghost av-btn--sm">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak
            </button>
        </div>
    </div>

    <div class="av-content">

        {{-- FILTER TANGGAL --}}
        <div class="av-card mb-4 no-print">
            <div class="av-card-body" style="padding:0.875rem 1.25rem;">
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:flex-end;">
                    <div>
                        <label class="av-label">Dari Tanggal</label>
                        <input wire:model.live="startDate" type="date" class="av-input" style="width:160px;">
                    </div>
                    <div>
                        <label class="av-label">Sampai Tanggal</label>
                        <input wire:model.live="endDate" type="date" class="av-input" style="width:160px;">
                    </div>

                    {{-- Shortcut range --}}
                    <div style="display:flex; gap:0.375rem; flex-wrap:wrap; padding-bottom:1px;">
                        <button
                            wire:click="$set('startDate', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                            class="av-btn av-btn--ghost av-btn--sm">Bulan Ini</button>
                        <button
                            wire:click="$set('startDate', '{{ now()->subDays(30)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                            class="av-btn av-btn--ghost av-btn--sm">30 Hari</button>
                        <button
                            wire:click="$set('startDate', '{{ now()->subDays(7)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                            class="av-btn av-btn--ghost av-btn--sm">7 Hari</button>
                        <button
                            wire:click="$set('startDate', '{{ now()->startOfYear()->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                            class="av-btn av-btn--ghost av-btn--sm">Tahun Ini</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRINT HEADER (hanya muncul saat cetak) --}}
        <div class="print-only" style="display:none; margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                <div
                    style="width:36px; height:36px; background:#2563eb; border-radius:8px; display:flex; align-items:center; justify-content:center; color:white; font-size:1rem;">
                    ✈</div>
                <div>
                    <div style="font-size:1rem; font-weight:600;">NCS LINE WORLD WIDE</div>
                    <div style="font-size:0.75rem; color:#64748b; letter-spacing:0.1em; text-transform:uppercase;">
                        AeroImport System</div>
                </div>
            </div>
            <div style="font-size:0.875rem; color:#64748b;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} —
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                &nbsp;·&nbsp; Dicetak: {{ now()->format('d M Y, H:i') }} WIB
            </div>
            <hr style="margin:1rem 0; border-color:#e2e8f0;">
        </div>

        {{-- STAT CARDS --}}
        <div wire:loading.class="opacity-50" class="av-grid-stats mb-4" style="transition:opacity 200ms ease;">

            {{-- Pendapatan --}}
            <div class="av-stat-card" style="border-color:var(--color-success-border);">
                <div class="av-stat-icon av-stat-icon--green">💰</div>
                <div class="av-stat-value" style="font-size:1.125rem;">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </div>
                <div class="av-stat-label">Total Pendapatan (Lunas)</div>
                <div class="av-stat-delta av-stat-delta--up" style="margin-top:0.375rem;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    {{ $transaksiSukses->count() }} transaksi dalam periode ini
                </div>
            </div>

            {{-- Total Pengajuan --}}
            <div class="av-stat-card">
                <div class="av-stat-icon av-stat-icon--blue">📋</div>
                <div class="av-stat-value">{{ $totalPengajuan }}</div>
                <div class="av-stat-label">Total Pengajuan Masuk</div>
            </div>

            {{-- Approved --}}
            <div class="av-stat-card" style="border-color:var(--color-success-border);">
                <div class="av-stat-icon av-stat-icon--green">✅</div>
                <div class="av-stat-value">{{ $totalApproved }}</div>
                <div class="av-stat-label">Berkas Disetujui</div>
                @if ($totalPengajuan > 0)
                    <div class="av-stat-delta av-stat-delta--up">
                        {{ round(($totalApproved / $totalPengajuan) * 100) }}% approval rate
                    </div>
                @endif
            </div>

            {{-- Rejected --}}
            <div class="av-stat-card" style="border-color:var(--color-danger-border);">
                <div class="av-stat-icon av-stat-icon--red">❌</div>
                <div class="av-stat-value">{{ $totalRejected }}</div>
                <div class="av-stat-label">Berkas Ditolak</div>
                @if ($totalPengajuan > 0)
                    <div class="av-stat-delta av-stat-delta--down">
                        {{ round(($totalRejected / $totalPengajuan) * 100) }}% rejection rate
                    </div>
                @endif
            </div>

        </div>

        {{-- TABEL TRANSAKSI --}}
        <div class="av-card">
            <div class="av-card-header">
                <div>
                    <span class="av-card-title">Transaksi Lunas</span>
                    <div style="font-size:0.6875rem; color:var(--color-text-muted); margin-top:2px;">
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} —
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </div>
                </div>
                <span class="av-badge av-badge--green">{{ $transaksiSukses->count() }} transaksi</span>
            </div>

            <div wire:loading.delay style="padding:0.75rem 1.25rem; display:flex; gap:0.5rem; align-items:center;">
                <div class="av-skeleton" style="width:16px; height:16px; border-radius:50%;"></div>
                <span class="text-muted" style="font-size:0.75rem;">Memperbarui data...</span>
            </div>

            <div wire:loading.remove>
                @if ($transaksiSukses->isEmpty())
                    <div class="av-empty">
                        <div class="av-empty-icon">📊</div>
                        <div class="av-empty-title">Tidak ada transaksi lunas</div>
                        <p style="font-size:0.8125rem; margin-top:0.25rem;">Tidak ditemukan transaksi dalam periode yang
                            dipilih.</p>
                    </div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="av-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No. Invoice</th>
                                    <th>Klien</th>
                                    <th>Metode</th>
                                    <th>Tanggal Bayar</th>
                                    <th style="text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksiSukses as $i => $trx)
                                    <tr>
                                        <td style="color:var(--color-text-muted); width:40px;">{{ $i + 1 }}</td>
                                        <td>
                                            <span class="col-primary" style="font-family:monospace; font-size:0.75rem;">
                                                {{ $trx->invoice_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                style="font-weight:500; color:var(--color-text-primary); font-size:0.8125rem;">
                                                {{ $trx->user->name }}
                                            </div>
                                            @if ($trx->user->company_name)
                                                <div style="font-size:0.6875rem; color:var(--color-text-muted);">
                                                    {{ $trx->user->company_name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($trx->payment_method)
                                                <span
                                                    class="av-badge av-badge--gray">{{ $trx->payment_method }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td style="font-size:0.8rem;">
                                            {{ $trx->payment_date ? \Carbon\Carbon::parse($trx->payment_date)->format('d M Y, H:i') : '—' }}
                                        </td>
                                        <td style="text-align:right; color:var(--color-success); font-weight:600;">
                                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- TOTAL ROW --}}
                    <div
                        style="padding:0.875rem 1rem; border-top:1px solid var(--color-border); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:0.8125rem; color:var(--color-text-muted);">
                            {{ $transaksiSukses->count() }} transaksi · periode
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d
                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                        <div style="text-align:right;">
                            <div style="font-size:0.6875rem; color:var(--color-text-muted); margin-bottom:1px;">Grand
                                Total</div>
                            <div style="font-size:1.125rem; font-weight:700; color:var(--color-success);">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- PRINT STYLES --}}
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .av-topbar {
                display: none !important;
            }

            .av-sidebar {
                display: none !important;
            }

            .av-main {
                margin-left: 0 !important;
            }

            .av-content {
                padding: 0 !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .av-card {
                border: 1px solid #e2e8f0 !important;
                background: white !important;
                break-inside: avoid;
            }

            .av-stat-card {
                background: white !important;
                border: 1px solid #e2e8f0 !important;
            }

            .av-stat-value,
            .av-card-title,
            .col-primary {
                color: #0f172a !important;
            }

            .av-table thead th,
            .av-table tbody td {
                color: #334155 !important;
                border-color: #e2e8f0 !important;
            }

            .av-badge,
            .av-pill {
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</div>
