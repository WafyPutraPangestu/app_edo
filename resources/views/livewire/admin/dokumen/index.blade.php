<div class="av-content">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2>Penerbitan e-DO</h2>
            <p class="text-muted text-xs mt-0.5">Pengajuan yang telah disetujui dan pembayaran lunas</p>
        </div>
        <span class="av-badge av-badge--green">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="12" r="6" />
            </svg>
            Siap Terbitkan
        </span>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="av-alert av-alert--success mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="av-alert av-alert--danger mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Card Utama --}}
    <div class="av-card">

        {{-- Card Header: Search --}}
        <div class="av-card-header">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" class="text-muted">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                <span class="av-card-title">Daftar Siap Terbitkan</span>
            </div>
            <div class="relative" style="width: 240px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" class="text-muted"
                    style="position:absolute; left:0.625rem; top:50%; transform:translateY(-50%); pointer-events:none;">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari AWB / nama klien…"
                    class="av-input" style="padding-left: 2rem;">
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="av-table">
                <thead>
                    <tr>
                        <th>No. AWB</th>
                        <th>Klien</th>
                        <th>Rute</th>
                        <th>Total Tagihan</th>
                        <th>Pembayaran</th>
                        <th>Status e-DO</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr wire:key="req-{{ $req->id }}">
                            <td>
                                <span class="col-primary font-mono text-xs">{{ $req->awb_number ?? '-' }}</span>
                                @if ($req->flight_number)
                                    <div class="text-muted" style="font-size:0.625rem; margin-top:2px;">✈
                                        {{ $req->flight_number }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-primary text-xs font-medium">{{ $req->user->name }}</div>
                                @if ($req->user->company_name)
                                    <div class="text-muted" style="font-size:0.625rem;">{{ $req->user->company_name }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($req->origin && $req->destination)
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-secondary">{{ $req->origin }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            class="text-muted">
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                            <polyline points="12 5 19 12 12 19" />
                                        </svg>
                                        <span class="text-secondary">{{ $req->destination }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($req->transaction)
                                    <span class="col-primary text-xs font-medium">
                                        Rp {{ number_format($req->transaction->total_amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($req->transaction && $req->transaction->payment_date)
                                    <div class="text-xs text-secondary">
                                        {{ \Carbon\Carbon::parse($req->transaction->payment_date)->format('d M Y') }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.625rem;">via
                                        {{ $req->transaction->payment_method ?? '—' }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($req->releaseDocument)
                                    <span class="av-pill av-pill--green">Diterbitkan</span>
                                    <div class="text-muted" style="font-size:0.625rem; margin-top:3px;">
                                        {{ \Carbon\Carbon::parse($req->releaseDocument->issued_date)->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="av-pill av-pill--amber">Belum Diterbitkan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.dokumen.terbitkan', $req->id) }}"
                                    class="av-btn av-btn--sm {{ $req->releaseDocument ? 'av-btn--secondary' : 'av-btn--primary' }}">
                                    @if ($req->releaseDocument)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        Lihat
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <polygon
                                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                        </svg>
                                        Terbitkan
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="av-empty">
                                    <div class="av-empty-icon">📭</div>
                                    <div class="av-empty-title">Tidak ada data</div>
                                    <p class="text-xs text-muted mt-1">
                                        @if ($search)
                                            Tidak ada hasil untuk pencarian "<strong>{{ $search }}</strong>"
                                        @else
                                            Belum ada pengajuan yang siap untuk diterbitkan e-DO-nya.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($requests->hasPages())
            <div class="av-card-body"
                style="border-top: 1px solid var(--color-border); padding-top: 0.75rem; padding-bottom: 0.75rem;">
                {{ $requests->links() }}
            </div>
        @endif

    </div>

</div>
