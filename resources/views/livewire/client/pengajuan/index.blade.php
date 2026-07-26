<div>
    <div class="flex items-center justify-between mb-6 gap-3">
        <div>
            <h1>Pengajuan Saya</h1>
            <p class="text-muted text-sm mt-1">Daftar seluruh pengajuan pelepasan dokumen Anda.</p>
        </div>
        <a href="{{ route('client.pengajuan.create') }}" wire:navigate class="av-btn av-btn--primary">
            + Ajukan Baru
        </a>
    </div>

    {{-- Stats --}}
    <div class="av-grid-stats mb-6">
        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--blue">📋</div>
            <div class="av-stat-value">{{ $stats['total'] }}</div>
            <div class="av-stat-label">Total Pengajuan</div>
        </div>
        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--amber">⏳</div>
            <div class="av-stat-value">{{ $stats['pending'] }}</div>
            <div class="av-stat-label">Menunggu Verifikasi</div>
        </div>
        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--blue">✅</div>
            <div class="av-stat-value">{{ $stats['approved'] }}</div>
            <div class="av-stat-label">Disetujui</div>
        </div>
        <div class="av-stat-card">
            <div class="av-stat-icon av-stat-icon--green">🏁</div>
            <div class="av-stat-value">{{ $stats['completed'] }}</div>
            <div class="av-stat-label">Selesai</div>
        </div>
    </div>

    <div class="av-card">
        <div class="av-card-header flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <input type="text" wire:model.live.debounce.400ms="search" class="av-input" style="max-width:260px;"
                    placeholder="Cari nomor AWB, penerbangan, barang...">
                <select wire:model.live="status" class="av-select" style="max-width:170px;">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>
            <select wire:model.live="sort" class="av-select" style="max-width:150px;">
                <option value="latest">Terbaru</option>
                <option value="oldest">Terlama</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="av-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. AWB</th>
                        <th>Rute</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr wire:key="req-{{ $req->id }}">
                            <td>{{ $req->created_at->format('d M Y') }}</td>
                            <td class="col-primary">{{ $req->awb_number ?: '—' }}</td>
                            <td>{{ $req->origin ?: '—' }} → {{ $req->destination ?: '—' }}</td>
                            <td>
                                @php
                                    $statusMap = [
                                        'pending' => ['amber', 'Menunggu Verifikasi'],
                                        'approved' => ['blue', 'Disetujui'],
                                        'rejected' => ['red', 'Ditolak'],
                                        'completed' => ['green', 'Selesai'],
                                    ];
                                    [$color, $label] = $statusMap[$req->status] ?? ['gray', $req->status];
                                @endphp
                                <span class="av-pill av-pill--{{ $color }}">{{ $label }}</span>
                            </td>
                            <td>
                                @if ($req->transaction)
                                    @php
                                        $payColor = match ($req->transaction->status) {
                                            'paid' => 'green',
                                            'failed' => 'red',
                                            default => 'amber',
                                        };
                                    @endphp
                                    <span
                                        class="av-badge av-badge--{{ $payColor }}">{{ ucfirst($req->transaction->status) }}</span>
                                @else
                                    <span class="text-muted text-xs">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('client.pengajuan.show', $req->id) }}" wire:navigate
                                    class="av-btn av-btn--ghost av-btn--sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="av-empty">
                                    <div class="av-empty-icon">📭</div>
                                    <div class="av-empty-title">Belum ada pengajuan</div>
                                    <p class="text-xs">Klik "Ajukan Baru" untuk membuat pengajuan pertama Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="px-4 py-3 border-t border-subtle">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
