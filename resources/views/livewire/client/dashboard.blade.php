<div class="av-content" x-data="{ activeTab: 'requests' }">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1>Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-muted text-sm mt-1">Pantau status pengajuan release dan tagihan Anda di sini</p>
        </div>
        <a href="{{ route('client.pengajuan.create') }}" class="av-btn av-btn--primary">
            + Buat Pengajuan Baru
        </a>
    </div>

    {{-- ══════════════ ALERT: TAGIHAN BELUM DIBAYAR ══════════════ --}}
    @if ($unpaidTransactions->count())
        <div class="av-card mb-6" style="border-color: var(--color-amber-border);">
            <div class="av-card-body">
                <div class="flex items-start gap-3">
                    <div class="av-stat-icon av-stat-icon--amber" style="margin-bottom:0;">⚠️</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between flex-wrap gap-2 mb-1">
                            <h3 class="text-amber">
                                Anda punya {{ $unpaidTransactions->count() }} tagihan belum dibayar
                            </h3>
                            <span class="av-badge av-badge--amber">
                                Total Rp{{ number_format($totalUnpaid, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-sm text-secondary mb-3">
                            Selesaikan pembayaran agar e-DO Anda segera diterbitkan.
                        </p>
                        <div class="space-y-2">
                            @foreach ($unpaidTransactions as $trx)
                                <div
                                    class="flex items-center justify-between bg-surface-3 rounded-md px-3 py-2 border border-subtle flex-wrap gap-2">
                                    <div>
                                        <span class="col-primary text-sm">{{ $trx->invoice_number }}</span>
                                        <span class="text-muted text-xs ml-2">
                                            AWB: {{ $trx->releaseRequest->awb_number ?? '—' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-primary text-sm font-medium">
                                            Rp{{ number_format($trx->total_amount, 0, ',', '.') }}
                                        </span>
                                        <a href="{{ route('client.tagihan.bayar', $trx->id) }}"
                                            class="av-btn av-btn--amber av-btn--sm">
                                            Bayar Sekarang
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════ STAT CARDS ══════════════ --}}
    <div class="av-grid-stats mb-6">

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--blue">📄</div>
            <div class="av-stat-value">{{ number_format($totalPengajuan) }}</div>
            <div class="av-stat-label">Total Pengajuan</div>
        </div>

        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--amber">⏳</div>
            <div class="av-stat-value">{{ number_format($pendingCount) }}</div>
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
            <div class="av-stat-icon av-stat-icon--green">💳</div>
            <div class="av-stat-value">Rp{{ number_format($totalPaid, 0, ',', '.') }}</div>
            <div class="av-stat-label">Total Sudah Dibayar</div>
        </div>

    </div>

    {{-- ══════════════ e-DO SIAP DIUNDUH ══════════════ --}}
    @if ($readyDocuments->count())
        <div class="av-card mb-6">
            <div class="av-card-header">
                <span class="av-card-title">📦 e-DO Siap Diunduh</span>
            </div>
            <div class="av-card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($readyDocuments as $req)
                        <div class="bg-surface-3 rounded-lg border border-subtle p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="av-pill av-pill--green">Selesai</span>
                                <span class="text-muted text-xs">
                                    {{ $req->releaseDocument->issued_date?->format('d M Y') ?? '—' }}
                                </span>
                            </div>
                            <div class="col-primary text-sm mb-1">AWB: {{ $req->awb_number ?? '—' }}</div>
                            <div class="text-muted text-xs mb-3">
                                {{ $req->origin ?? '—' }} → {{ $req->destination ?? '—' }}
                            </div>
                            <a href="{{ route('download.edo', $req->releaseDocument->id) }}"
                                class="av-btn av-btn--primary av-btn--sm w-full justify-center">
                                ⬇ Unduh PDF e-DO
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════ RIWAYAT PENGAJUAN ══════════════ --}}
    <div class="av-card">
        <div class="av-card-header">
            <span class="av-card-title">Riwayat Pengajuan</span>
            <a href="{{ route('client.pengajuan.index') }}" class="av-btn av-btn--ghost av-btn--sm">
                Lihat Semua →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="av-table">
                <thead>
                    <tr>
                        <th>No. AWB</th>
                        <th>Rute</th>
                        <th>Status Verifikasi</th>
                        <th>Status Pembayaran</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $req)
                        <tr>
                            <td class="col-primary">{{ $req->awb_number ?? 'Belum diisi' }}</td>
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
                                @if ($req->status === 'rejected' && $req->rejection_note)
                                    <div class="text-muted text-xs mt-1" data-tooltip="{{ $req->rejection_note }}">
                                        ℹ️ Lihat alasan
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if (!$req->transaction)
                                    <span class="av-pill av-pill--gray">Belum Ada Tagihan</span>
                                @elseif($req->transaction->status === 'unpaid')
                                    <span class="av-pill av-pill--amber">Belum Dibayar</span>
                                @elseif($req->transaction->status === 'paid')
                                    <span class="av-pill av-pill--green">Lunas</span>
                                @else
                                    <span class="av-pill av-pill--red">Gagal</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $req->created_at->diffForHumans() }}</td>
                            <td>
                                @if ($req->releaseDocument)
                                    <a href="{{ route('download.edo', $req->releaseDocument->id) }}"
                                        class="av-btn av-btn--secondary av-btn--sm">
                                        Unduh e-DO
                                    </a>
                                @elseif($req->transaction && $req->transaction->status === 'unpaid')
                                    <a href="{{ route('client.transactions.pay', $req->transaction->id) }}"
                                        class="av-btn av-btn--amber av-btn--sm">
                                        Bayar
                                    </a>
                                @else
                                    <a href="{{ route('client.release-requests.show', $req->id) }}"
                                        class="av-btn av-btn--ghost av-btn--sm">
                                        Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="av-empty">
                                    <div class="av-empty-icon">📭</div>
                                    <div class="av-empty-title">Belum ada pengajuan</div>
                                    <p class="text-sm mb-4">Mulai buat pengajuan release pertama Anda</p>
                                    <a href="{{ route('client.pengajuan.index') }}"
                                        class="av-btn av-btn--primary av-btn--sm">
                                        + Buat Pengajuan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
