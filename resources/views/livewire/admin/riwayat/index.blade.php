<div>
    {{-- TOPBAR --}}
    <div class="av-topbar">
        <span class="av-page-title">Riwayat Transaksi</span>
        <div class="av-topbar-actions">
            <span class="text-muted text-xs">{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="av-content">

        {{-- FILTER BAR --}}
        <div class="av-card mb-4">
            <div class="av-card-body" style="padding: 0.875rem 1.25rem;">
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                    {{-- Search --}}
                    <div style="position:relative; flex:1; min-width:200px;">
                        <svg style="position:absolute; left:0.625rem; top:50%; transform:translateY(-50%); width:14px; height:14px; color:var(--color-text-muted);"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Cari nomor invoice atau nama klien..." class="av-input"
                            style="padding-left:2rem;">
                    </div>

                    {{-- Status Filter --}}
                    <select wire:model.live="statusFilter" class="av-select" style="width:auto; min-width:150px;">
                        <option value="">Semua Status</option>
                        <option value="unpaid">Belum Bayar</option>
                        <option value="paid">Lunas</option>
                        <option value="failed">Gagal</option>
                    </select>

                    {{-- Reset --}}
                    @if ($search || $statusFilter)
                        <button wire:click="$set('search', ''); $set('statusFilter', '')"
                            class="av-btn av-btn--ghost av-btn--sm">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reset
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="av-card">
            <div class="av-card-header">
                <span class="av-card-title">
                    Daftar Transaksi
                </span>
                <span class="av-badge av-badge--blue">
                    {{ $transactions->total() }} total
                </span>
            </div>

            {{-- Loading state --}}
            <div wire:loading.delay wire:target="search, statusFilter" style="padding:0.5rem 1.25rem;">
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    <div class="av-skeleton" style="width:16px; height:16px; border-radius:50%;"></div>
                    <span class="text-muted" style="font-size:0.75rem;">Memuat data...</span>
                </div>
            </div>

            <div wire:loading.remove wire:target="search, statusFilter">
                @if ($transactions->isEmpty())
                    <div class="av-empty">
                        <div class="av-empty-icon">🗃️</div>
                        <div class="av-empty-title">Belum ada transaksi</div>
                        <p style="font-size:0.8125rem; margin-top:0.25rem;">
                            @if ($search || $statusFilter)
                                Tidak ada hasil untuk filter yang dipilih.
                            @else
                                Transaksi akan muncul setelah klien melakukan pembayaran.
                            @endif
                        </p>
                    </div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="av-table">
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Klien</th>
                                    <th>Total Tagihan</th>
                                    <th>Metode Bayar</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th style="text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $trx)
                                    <tr x-data="{}"
                                        @click="window.location='{{ route('admin.riwayat.show', $trx->id) }}'"
                                        style="cursor:pointer;">
                                        <td>
                                            <span class="col-primary"
                                                style="font-family: monospace; font-size: 0.75rem;">
                                                {{ $trx->invoice_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                                <div class="av-avatar"
                                                    style="width:28px; height:28px; font-size:0.625rem; flex-shrink:0;">
                                                    {{ strtoupper(substr($trx->user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div
                                                        style="color:var(--color-text-primary); font-size:0.8125rem; font-weight:500;">
                                                        {{ $trx->user->name }}
                                                    </div>
                                                    @if ($trx->user->company_name)
                                                        <div
                                                            style="font-size:0.6875rem; color:var(--color-text-muted);">
                                                            {{ $trx->user->company_name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="color:var(--color-text-primary); font-weight:500;">
                                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($trx->payment_method)
                                                <span class="av-badge av-badge--gray">{{ $trx->payment_method }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($trx->status === 'paid')
                                                <span class="av-pill av-pill--green">Lunas</span>
                                            @elseif($trx->status === 'unpaid')
                                                <span class="av-pill av-pill--amber">Belum Bayar</span>
                                            @else
                                                <span class="av-pill av-pill--red">Gagal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size:0.8rem;">
                                                {{ $trx->created_at->format('d M Y') }}
                                            </div>
                                            <div style="font-size:0.6875rem; color:var(--color-text-muted);">
                                                {{ $trx->created_at->format('H:i') }} WIB
                                            </div>
                                        </td>
                                        <td style="text-align:right;" @click.stop="">
                                            <a href="{{ route('admin.riwayat.show', $trx->id) }}"
                                                class="av-btn av-btn--secondary av-btn--sm">
                                                Detail
                                                <svg width="12" height="12" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if ($transactions->hasPages())
                        <div
                            style="padding:0.875rem 1.25rem; border-top:1px solid var(--color-border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                            <span style="font-size:0.75rem; color:var(--color-text-muted);">
                                Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari
                                {{ $transactions->total() }} transaksi
                            </span>
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>
