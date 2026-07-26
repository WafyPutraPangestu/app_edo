<div>
    {{-- TOPBAR --}}
    <div class="av-topbar">
        <a href="{{ route('admin.riwayat.index') }}" class="av-btn av-btn--ghost av-btn--sm" style="gap:0.375rem;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <span class="av-page-title" style="flex:1; text-align:left; margin-left:0.75rem;">
            Detail Transaksi
        </span>
        <div class="av-topbar-actions">
            @if ($transaction->status === 'paid' && $transaction->releaseRequest->releaseDocument)
                <a href="{{ Storage::url($transaction->releaseRequest->releaseDocument->pdf_path) }}" target="_blank"
                    class="av-btn av-btn--primary av-btn--sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh e-DO
                </a>
            @endif
        </div>
    </div>

    <div class="av-content">
        <div style="display:grid; grid-template-columns:1fr 340px; gap:1rem; align-items:start;">

            {{-- KIRI --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- INFO INVOICE --}}
                <div class="av-card">
                    <div class="av-card-header">
                        <span class="av-card-title">Informasi Invoice</span>
                        @if ($transaction->status === 'paid')
                            <span class="av-pill av-pill--green">Lunas</span>
                        @elseif($transaction->status === 'unpaid')
                            <span class="av-pill av-pill--amber">Belum Dibayar</span>
                        @else
                            <span class="av-pill av-pill--red">Gagal</span>
                        @endif
                    </div>
                    <div class="av-card-body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                            <div>
                                <div class="av-label">Nomor Invoice</div>
                                <div
                                    style="color:var(--color-text-primary); font-weight:500; font-family:monospace; font-size:0.9rem;">
                                    {{ $transaction->invoice_number }}
                                </div>
                            </div>
                            <div>
                                <div class="av-label">Total Tagihan</div>
                                <div style="color:var(--color-accent-glow); font-weight:600; font-size:1.125rem;">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="av-label">Metode Pembayaran</div>
                                <div style="color:var(--color-text-primary);">
                                    {{ $transaction->payment_method ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="av-label">Tanggal Bayar</div>
                                <div style="color:var(--color-text-primary);">
                                    {{ $transaction->payment_date
                                        ? \Carbon\Carbon::parse($transaction->payment_date)->format('d M Y, H:i') . ' WIB'
                                        : '—' }}
                                </div>
                            </div>
                            @if ($transaction->midtrans_transaction_id)
                                <div style="grid-column:1/-1;">
                                    <div class="av-label">Midtrans Transaction ID</div>
                                    <div
                                        style="color:var(--color-text-secondary); font-family:monospace; font-size:0.75rem;">
                                        {{ $transaction->midtrans_transaction_id }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RINCIAN BIAYA --}}
                <div class="av-card">
                    <div class="av-card-header">
                        <span class="av-card-title">Rincian Local Charges</span>
                        <span class="av-badge av-badge--blue">
                            {{ $transaction->releaseRequest->requestCharges->count() }} item
                        </span>
                    </div>
                    @if ($transaction->releaseRequest->requestCharges->isEmpty())
                        <div class="av-empty" style="padding:2rem 1rem;">
                            <div class="av-empty-title">Belum ada rincian biaya</div>
                        </div>
                    @else
                        <table class="av-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Biaya</th>
                                    <th style="text-align:right;">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->releaseRequest->requestCharges as $i => $charge)
                                    <tr>
                                        <td style="color:var(--color-text-muted); width:40px;">{{ $i + 1 }}</td>
                                        <td class="col-primary">{{ $charge->localCharge->name }}</td>
                                        <td style="text-align:right; color:var(--color-text-primary); font-weight:500;">
                                            Rp {{ number_format($charge->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div
                            style="padding:0.75rem 1rem; border-top:1px solid var(--color-border); display:flex; justify-content:space-between; align-items:center;">
                            <span
                                style="font-size:0.8125rem; font-weight:500; color:var(--color-text-secondary);">Total</span>
                            <span style="font-size:1rem; font-weight:600; color:var(--color-accent-glow);">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- e-DO SECTION --}}
                @php $edo = $transaction->releaseRequest->releaseDocument; @endphp
                @if ($edo)
                    <div class="av-card" style="border-color:var(--color-success-border);">
                        <div class="av-card-header">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="av-stat-icon av-stat-icon--green"
                                    style="margin-bottom:0; width:28px; height:28px; font-size:0.875rem;">✓</div>
                                <span class="av-card-title">e-Delivery Order Terbit</span>
                            </div>
                            <span class="av-pill av-pill--green">Aktif</span>
                        </div>
                        <div class="av-card-body">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div>
                                    <div class="av-label">Tanggal Terbit</div>
                                    <div style="color:var(--color-text-primary);">
                                        {{ $edo->issued_date ? \Carbon\Carbon::parse($edo->issued_date)->format('d M Y, H:i') . ' WIB' : '—' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="av-label">QR Code String</div>
                                    <div
                                        style="color:var(--color-text-secondary); font-family:monospace; font-size:0.6875rem; word-break:break-all;">
                                        {{ Str::limit($edo->qr_code_string, 40) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- KANAN --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- INFO KLIEN --}}
                <div class="av-card">
                    <div class="av-card-header">
                        <span class="av-card-title">Informasi Klien</span>
                    </div>
                    <div class="av-card-body">
                        <div style="display:flex; align-items:center; gap:0.875rem; margin-bottom:1rem;">
                            <div class="av-avatar" style="width:44px; height:44px; font-size:0.875rem; flex-shrink:0;">
                                {{ strtoupper(substr($transaction->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="color:var(--color-text-primary); font-weight:600; font-size:0.9375rem;">
                                    {{ $transaction->user->name }}
                                </div>
                                <div style="font-size:0.75rem; color:var(--color-text-muted);">
                                    {{ $transaction->user->email }}
                                </div>
                            </div>
                        </div>

                        @if ($transaction->user->company_name)
                            <div style="margin-bottom:0.625rem;">
                                <div class="av-label">Perusahaan</div>
                                <div style="color:var(--color-text-primary);">{{ $transaction->user->company_name }}
                                </div>
                            </div>
                        @endif

                        @if ($transaction->user->phone)
                            <div>
                                <div class="av-label">Telepon</div>
                                <div style="color:var(--color-text-primary);">{{ $transaction->user->phone }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- INFO PENGAJUAN --}}
                <div class="av-card">
                    <div class="av-card-header">
                        <span class="av-card-title">Dokumen Pengajuan</span>
                        @php $req = $transaction->releaseRequest; @endphp
                        @if ($req->status === 'approved')
                            <span class="av-pill av-pill--green">Disetujui</span>
                        @elseif($req->status === 'pending')
                            <span class="av-pill av-pill--amber">Pending</span>
                        @else
                            <span class="av-pill av-pill--red">Ditolak</span>
                        @endif
                    </div>
                    <div class="av-card-body" style="display:flex; flex-direction:column; gap:0.75rem;">
                        <div>
                            <div class="av-label">ID Pengajuan</div>
                            <div style="color:var(--color-text-primary); font-family:monospace;">#{{ $req->id }}
                            </div>
                        </div>
                        <div>
                            <div class="av-label">Tanggal Pengajuan</div>
                            <div style="color:var(--color-text-primary);">
                                {{ $req->created_at->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                        <div class="av-divider" style="margin:0.25rem 0;"></div>

                        {{-- File links --}}
                        <div>
                            <div class="av-label" style="margin-bottom:0.5rem;">Berkas Unggahan</div>
                            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                                <a href="{{ Storage::url($req->surat_kuasa_path) }}" target="_blank"
                                    class="av-btn av-btn--ghost av-btn--sm" style="justify-content:flex-start;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Surat Kuasa
                                </a>
                                {{-- <a href="{{ Storage::url($req->awb_path) }}" target="_blank"
                                    class="av-btn av-btn--ghost av-btn--sm" style="justify-content:flex-start;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Air Waybill (AWB)
                                </a> --}}
                            </div>
                        </div>

                        @if ($req->rejection_note)
                            <div class="av-alert av-alert--danger" style="font-size:0.75rem;">
                                <svg width="14" height="14" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $req->rejection_note }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TIMELINE --}}
                <div class="av-card">
                    <div class="av-card-header">
                        <span class="av-card-title">Timeline</span>
                    </div>
                    <div class="av-card-body">
                        <div style="display:flex; flex-direction:column; gap:0; position:relative;">
                            @php
                                $steps = [
                                    [
                                        'label' => 'Pengajuan Dibuat',
                                        'time' => $req->created_at,
                                        'done' => true,
                                        'color' => 'blue',
                                    ],
                                    [
                                        'label' => 'Berkas Disetujui',
                                        'time' => $req->updated_at,
                                        'done' => $req->status === 'approved',
                                        'color' => 'green',
                                    ],
                                    [
                                        'label' => 'Pembayaran Lunas',
                                        'time' => $transaction->payment_date,
                                        'done' => $transaction->status === 'paid',
                                        'color' => 'green',
                                    ],
                                    [
                                        'label' => 'e-DO Terbit',
                                        'time' => optional($edo)->issued_date,
                                        'done' => (bool) $edo,
                                        'color' => 'amber',
                                    ],
                                ];
                            @endphp

                            @foreach ($steps as $i => $step)
                                <div
                                    style="display:flex; gap:0.75rem; align-items:flex-start; {{ !$loop->last ? 'padding-bottom:1rem;' : '' }}">
                                    {{-- dot & line --}}
                                    <div
                                        style="display:flex; flex-direction:column; align-items:center; flex-shrink:0;">
                                        <div
                                            style="
                                            width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.625rem;
                                            {{ $step['done']
                                                ? 'background:var(--color-' .
                                                    ($step['color'] === 'amber'
                                                        ? 'amber-subtle); color:var(--color-amber-light); border:1px solid var(--color-amber-border'
                                                        : ($step['color'] === 'green'
                                                            ? 'success-subtle); color:var(--color-success); border:1px solid var(--color-success-border'
                                                            : 'accent-subtle); color:var(--color-accent-glow); border:1px solid var(--color-accent-border')) .
                                                    ');'
                                                : 'background:var(--color-surface-3); color:var(--color-text-muted); border:1px solid var(--color-border-medium);' }}
                                        ">
                                            @if ($step['done'])
                                                ✓
                                            @else
                                                {{ $i + 1 }}
                                            @endif
                                        </div>
                                        @if (!$loop->last)
                                            <div
                                                style="width:1px; flex:1; min-height:16px; background:var(--color-border); margin:2px 0;">
                                            </div>
                                        @endif
                                    </div>
                                    {{-- text --}}
                                    <div style="padding-top:1px;">
                                        <div
                                            style="font-size:0.8rem; font-weight:500; color:{{ $step['done'] ? 'var(--color-text-primary)' : 'var(--color-text-muted)' }};">
                                            {{ $step['label'] }}
                                        </div>
                                        @if ($step['done'] && $step['time'])
                                            <div
                                                style="font-size:0.6875rem; color:var(--color-text-muted); margin-top:1px;">
                                                {{ \Carbon\Carbon::parse($step['time'])->format('d M Y, H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
