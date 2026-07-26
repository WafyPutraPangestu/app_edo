<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.pengajuan.index') }}" wire:navigate class="av-icon-btn">←</a>
        <div class="flex-1">
            <h1>Detail Pengajuan #{{ $releaseRequest->id }}</h1>
            <p class="text-muted text-sm mt-1">Diajukan pada {{ $releaseRequest->created_at->format('d M Y, H:i') }}</p>
        </div>
        @php
            $statusMap = [
                'pending' => ['amber', 'Menunggu Verifikasi'],
                'approved' => ['blue', 'Disetujui'],
                'rejected' => ['red', 'Ditolak'],
                'completed' => ['green', 'Selesai'],
            ];
            [$color, $label] = $statusMap[$releaseRequest->status] ?? ['gray', $releaseRequest->status];
        @endphp
        <span class="av-pill av-pill--{{ $color }}">{{ $label }}</span>
    </div>

    @if ($releaseRequest->status === 'rejected' && $releaseRequest->rejection_note)
        <div class="av-alert av-alert--danger mb-4">
            <span>⚠️</span>
            <div>
                <strong class="block mb-1">Pengajuan Ditolak</strong>
                {{ $releaseRequest->rejection_note }}
            </div>
        </div>
    @endif

    {{-- Timeline --}}
    <div class="av-card mb-6">
        <div class="av-card-body">
            @php
                $steps = [
                    'submitted' => true,
                    'verified' => in_array($releaseRequest->status, ['approved', 'completed']),
                    'paid' => $releaseRequest->transaction?->status === 'paid',
                    'done' => $releaseRequest->status === 'completed',
                ];
                $labels = [
                    'submitted' => 'Diajukan',
                    'verified' => 'Diverifikasi',
                    'paid' => 'Dibayar',
                    'done' => 'Dokumen Terbit',
                ];
            @endphp
            <div class="flex items-center">
                @foreach ($labels as $key => $label)
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="av-avatar"
                            style="{{ $steps[$key] ? 'background:var(--color-success);color:#fff;border-color:transparent;' : '' }}">
                            {{ $steps[$key] ? '✓' : '' }}
                        </div>
                        <span
                            class="text-xs mt-2 {{ $steps[$key] ? 'text-primary' : 'text-muted' }}">{{ $label }}</span>
                    </div>
                    @if (!$loop->last)
                        <div class="flex-1 h-px"
                            style="background: {{ $steps[$key] ? 'var(--color-success)' : 'var(--color-border)' }}; margin-bottom: 1.25rem;">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Detail --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="av-card">
                <div class="av-card-header"><span class="av-card-title">Detail Kargo</span></div>
                <div class="av-card-body">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-muted block text-xs mb-1">Nomor AWB</span><span
                                class="text-primary">{{ $releaseRequest->awb_number ?: '—' }}</span></div>
                        <div><span class="text-muted block text-xs mb-1">Penerbangan</span><span
                                class="text-primary">{{ $releaseRequest->flight_number ?: '—' }}</span></div>
                        <div><span class="text-muted block text-xs mb-1">Asal</span><span
                                class="text-primary">{{ $releaseRequest->origin ?: '—' }}</span></div>
                        <div><span class="text-muted block text-xs mb-1">Tujuan</span><span
                                class="text-primary">{{ $releaseRequest->destination ?: '—' }}</span></div>
                        <div><span class="text-muted block text-xs mb-1">Jumlah Koli</span><span
                                class="text-primary">{{ $releaseRequest->quantity ?: '—' }}</span></div>
                        <div><span class="text-muted block text-xs mb-1">Berat Kotor</span><span
                                class="text-primary">{{ $releaseRequest->gross_weight ?: '—' }}</span></div>
                        <div class="col-span-2"><span class="text-muted block text-xs mb-1">Deskripsi Barang</span><span
                                class="text-primary">{{ $releaseRequest->goods_description ?: '—' }}</span></div>
                    </div>
                    <div class="av-divider"></div>
                    <a href="{{ asset('storage/' . $releaseRequest->surat_kuasa_path) }}" target="_blank"
                        class="av-btn av-btn--secondary av-btn--sm">
                        📄 Lihat Surat Kuasa
                    </a>
                </div>
            </div>

            @if ($releaseRequest->requestCharges->isNotEmpty())
                <div class="av-card">
                    <div class="av-card-header"><span class="av-card-title">Rincian Tagihan</span></div>
                    <div class="overflow-x-auto">
                        <table class="av-table">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th style="text-align:right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($releaseRequest->requestCharges as $charge)
                                    <tr>
                                        <td>{{ $charge->localCharge->name }}</td>
                                        <td style="text-align:right">Rp
                                            {{ number_format($charge->amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="col-primary">Total</td>
                                    <td class="col-primary" style="text-align:right">
                                        Rp
                                        {{ number_format($releaseRequest->requestCharges->sum('amount'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Aksi --}}
        <div class="space-y-6">
            <div class="av-card">
                <div class="av-card-header"><span class="av-card-title">Aksi</span></div>
                <div class="av-card-body space-y-3">
                    @if (
                        $releaseRequest->status === 'approved' &&
                            (!$releaseRequest->transaction || $releaseRequest->transaction->status !== 'paid'))
                        <a href="{{ route('client.tagihan.bayar', $releaseRequest->id) }}" wire:navigate
                            class="av-btn av-btn--primary" style="width:100%;">
                            💳 Bayar Sekarang
                        </a>
                    @endif

                    @if ($releaseRequest->transaction?->status === 'paid' && $releaseRequest->releaseDocument)
                        <a href="{{ route('download.edo', $releaseRequest->releaseDocument->id) }}" target="_blank"
                            class="av-btn av-btn--secondary" style="width:100%;">
                            📥 Download e-DO
                        </a>
                        <a href="{{ route('download.invoice', $releaseRequest->releaseDocument->id) }}" target="_blank"
                            class="av-btn av-btn--secondary" style="width:100%;">
                            🧾 Download Invoice
                        </a>
                    @endif

                    @if ($releaseRequest->awb_path)
                        <a href="{{ route('download.awb', $releaseRequest->id) }}" class="av-btn av-btn--secondary"
                            style="width:100%;">
                            ✈️ Download AWB
                        </a>
                    @elseif ($releaseRequest->transaction?->status === 'paid')
                        <div class="av-alert av-alert--info">
                            <span>ℹ️</span>
                            <span class="text-xs">AWB sedang disiapkan admin.</span>
                        </div>
                    @endif

                    @if ($releaseRequest->status === 'pending')
                        <div class="av-alert av-alert--warning">
                            <span>⏳</span>
                            <span class="text-xs">Menunggu verifikasi admin.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
