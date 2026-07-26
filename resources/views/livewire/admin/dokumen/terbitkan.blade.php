<div class="av-content" style="max-width: 900px; margin: 0 auto;">

    {{-- Back nav --}}
    <a href="{{ route('admin.dokumen.index') }}"
        class="flex items-center gap-1.5 text-muted text-xs mb-5 w-fit hover:text-secondary"
        style="transition: color var(--transition-fast);">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6" />
        </svg>
        Kembali ke Daftar
    </a>

    {{-- Flash --}}
    @if (session()->has('message'))
        <div class="av-alert av-alert--success mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
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

    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="flex items-center gap-2">
                Penerbitan e-DO
                @if ($requestData->releaseDocument)
                    <span class="av-pill av-pill--green" style="font-size: 0.6875rem;">Sudah Diterbitkan</span>
                @else
                    <span class="av-pill av-pill--amber" style="font-size: 0.6875rem;">Belum Diterbitkan</span>
                @endif
            </h2>
            <p class="text-muted text-xs mt-1">Electronic Delivery Order —
                {{ $requestData->awb_number ?? 'No AWB Tidak Ada' }}</p>
        </div>
    </div>

    <div class="grid gap-4" style="grid-template-columns: 1fr 340px;">

        {{-- Kolom Kiri: Detail Kargo --}}
        <div class="flex flex-col gap-4">

            {{-- Info Kargo --}}
            <div class="av-card">
                <div class="av-card-header">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            style="color: var(--color-accent-glow)">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span class="av-card-title">Detail Kargo</span>
                    </div>
                    <span class="av-badge av-badge--blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        Approved
                    </span>
                </div>
                <div class="av-card-body">
                    <div class="grid gap-3" style="grid-template-columns: 1fr 1fr;">
                        @php
                            $fields = [
                                ['label' => 'No. AWB', 'value' => $requestData->awb_number, 'mono' => true],
                                ['label' => 'No. Penerbangan', 'value' => $requestData->flight_number, 'mono' => true],
                                ['label' => 'Origin', 'value' => $requestData->origin],
                                ['label' => 'Destination', 'value' => $requestData->destination],
                                ['label' => 'Kuantitas', 'value' => $requestData->quantity],
                                ['label' => 'Gross Weight', 'value' => $requestData->gross_weight],
                            ];
                        @endphp
                        @foreach ($fields as $field)
                            <div>
                                <div class="av-label" style="margin-bottom: 2px;">{{ $field['label'] }}</div>
                                <div
                                    class="text-primary text-xs font-medium {{ $field['mono'] ?? false ? 'font-mono' : '' }}">
                                    {{ $field['value'] ?? '—' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($requestData->goods_description)
                        <div class="av-divider" style="margin: 0.875rem 0;"></div>
                        <div>
                            <div class="av-label" style="margin-bottom: 2px;">Deskripsi Barang</div>
                            <div class="text-primary text-xs">{{ $requestData->goods_description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Klien --}}
            <div class="av-card">
                <div class="av-card-header">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            style="color: var(--color-accent-glow)">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span class="av-card-title">Data Klien</span>
                    </div>
                </div>
                <div class="av-card-body">
                    <div class="grid gap-3" style="grid-template-columns: 1fr 1fr;">
                        <div>
                            <div class="av-label" style="margin-bottom: 2px;">Nama</div>
                            <div class="text-primary text-xs font-medium">{{ $requestData->user->name }}</div>
                        </div>
                        <div>
                            <div class="av-label" style="margin-bottom: 2px;">Email</div>
                            <div class="text-primary text-xs">{{ $requestData->user->email }}</div>
                        </div>
                        @if ($requestData->user->company_name)
                            <div>
                                <div class="av-label" style="margin-bottom: 2px;">Perusahaan</div>
                                <div class="text-primary text-xs">{{ $requestData->user->company_name }}</div>
                            </div>
                        @endif
                        @if ($requestData->user->phone)
                            <div>
                                <div class="av-label" style="margin-bottom: 2px;">Telepon</div>
                                <div class="text-primary text-xs">{{ $requestData->user->phone }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Rincian Tagihan --}}
            <div class="av-card">
                <div class="av-card-header">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            style="color: var(--color-success)">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                        </svg>
                        <span class="av-card-title">Rincian Tagihan</span>
                    </div>
                    <span class="av-pill av-pill--green">Lunas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="av-table">
                        <thead>
                            <tr>
                                <th>Jenis Biaya</th>
                                <th style="text-align:right;">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requestData->requestCharges as $charge)
                                <tr>
                                    <td>{{ $charge->localCharge->name ?? '—' }}</td>
                                    <td style="text-align:right;" class="col-primary font-mono text-xs">
                                        Rp {{ number_format($charge->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada rincian biaya.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($requestData->transaction)
                            <tfoot>
                                <tr style="border-top: 1px solid var(--color-border-medium);">
                                    <td class="text-xs font-medium" style="color: var(--color-text-secondary);">Total
                                        Pembayaran</td>
                                    <td style="text-align:right;">
                                        <span class="text-primary font-medium font-mono text-xs">
                                            Rp
                                            {{ number_format($requestData->transaction->total_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                @if ($requestData->transaction)
                    <div class="av-card-body"
                        style="border-top: 1px solid var(--color-border); padding-top:0.75rem; padding-bottom:0.75rem;">
                        <div class="flex items-center justify-between text-xs text-muted">
                            <span>No. Invoice: <span
                                    class="font-mono text-secondary">{{ $requestData->transaction->invoice_number }}</span></span>
                            @if ($requestData->transaction->payment_date)
                                <span>Dibayar:
                                    {{ \Carbon\Carbon::parse($requestData->transaction->payment_date)->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Kolom Kanan: Panel e-DO --}}
        <div class="flex flex-col gap-4">

            {{-- Status e-DO --}}
            <div class="av-card">
                <div class="av-card-header">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            style="color: var(--color-accent-glow)">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="9" y1="13" x2="15" y2="13" />
                            <line x1="9" y1="17" x2="12" y2="17" />
                        </svg>
                        <span class="av-card-title">Dokumen e-DO</span>
                    </div>
                </div>

                @if ($requestData->releaseDocument)
                    {{-- Sudah ada dokumen --}}
                    <div class="av-card-body">
                        {{-- QR Code Preview --}}
                        <div class="flex flex-col items-center gap-3 mb-4">
                            <div
                                style="
                                background: white;
                                padding: 12px;
                                border-radius: var(--radius-lg);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                {!! QrCode::size(140)->generate($requestData->releaseDocument->qr_code_string) !!}
                            </div>
                            <div class="text-center">
                                <div class="text-muted text-xs">QR Code String</div>
                                <div class="font-mono text-xs text-primary mt-0.5"
                                    style="word-break: break-all; font-size: 0.625rem; color: var(--color-accent-glow);">
                                    {{ $requestData->releaseDocument->qr_code_string }}
                                </div>
                            </div>
                        </div>

                        <div class="av-divider"></div>

                        <div class="grid gap-2 mb-4">
                            <div class="flex justify-between text-xs">
                                <span class="text-muted">Diterbitkan</span>
                                <span
                                    class="text-secondary font-medium">{{ \Carbon\Carbon::parse($requestData->releaseDocument->issued_date)->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-muted">ID Dokumen</span>
                                <span class="text-secondary font-mono">#{{ $requestData->releaseDocument->id }}</span>
                            </div>
                        </div>

                        {{-- Tombol Unduh PDF (jika pdf_path tersedia) --}}
                        @if ($requestData->releaseDocument->pdf_path)
                            <a href="{{ route('admin.dokumen.download', $requestData->releaseDocument->id) }}"
                                class="av-btn av-btn--primary w-full" style="width:100%; justify-content:center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Unduh e-DO PDF
                            </a>
                        @else
                            <div class="av-alert av-alert--info" style="font-size:0.6875rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                QR Code tersimpan. PDF dapat dicetak oleh klien via halaman unduhan.
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Belum ada dokumen: Tombol Generate --}}
                    <div class="av-card-body">
                        <div class="av-empty" style="padding: 2rem 1rem;">
                            <div class="av-empty-icon" style="font-size: 2rem;">🔖</div>
                            <div class="av-empty-title" style="font-size: 0.875rem;">Belum Diterbitkan</div>
                            <p class="text-xs text-muted mt-1 mb-4">
                                Klik tombol di bawah untuk menerbitkan e-DO beserta QR Code unik untuk pengajuan ini.
                            </p>
                        </div>

                        <div class="av-alert av-alert--warning mb-4"
                            style="font-size:0.6875rem; align-items:flex-start;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                style="flex-shrink:0; margin-top:1px;">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            <span>Tindakan ini <strong>tidak dapat dibatalkan</strong>. Pastikan semua data kargo dan
                                pembayaran sudah benar sebelum menerbitkan.</span>
                        </div>

                        <button wire:click="generateEdo"
                            wire:confirm="Anda yakin ingin menerbitkan e-DO untuk AWB {{ $requestData->awb_number }}? Tindakan ini permanen."
                            wire:loading.attr="disabled" class="av-btn av-btn--primary"
                            style="width:100%; justify-content:center;">
                            <span wire:loading.remove wire:target="generateEdo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                Terbitkan e-DO Sekarang
                            </span>
                            <span wire:loading wire:target="generateEdo" class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    style="animation: spin 1s linear infinite;">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                </svg>
                                Memproses…
                            </span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Checklist Persyaratan --}}
            <div class="av-card">
                <div class="av-card-header">
                    <span class="av-card-title">Checklist Penerbitan</span>
                </div>
                <div class="av-card-body" style="display:flex; flex-direction:column; gap:0.625rem;">
                    @php
                        $checks = [
                            ['label' => 'Berkas disetujui Admin', 'ok' => $requestData->status === 'approved'],
                            ['label' => 'Tagihan sudah dibuat', 'ok' => $requestData->requestCharges->count() > 0],
                            [
                                'label' => 'Pembayaran lunas',
                                'ok' => $requestData->transaction && $requestData->transaction->status === 'paid',
                            ],
                            ['label' => 'e-DO sudah diterbitkan', 'ok' => (bool) $requestData->releaseDocument],
                        ];
                    @endphp
                    @foreach ($checks as $check)
                        <div class="flex items-center gap-2 text-xs">
                            @if ($check['ok'])
                                <div
                                    style="width:18px; height:18px; border-radius:50%; background:var(--color-success-subtle); border:1px solid var(--color-success-border); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                        style="color:var(--color-success)">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <span style="color:var(--color-success);">{{ $check['label'] }}</span>
                            @else
                                <div
                                    style="width:18px; height:18px; border-radius:50%; background:rgba(100,116,139,0.1); border:1px solid var(--color-border-medium); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <div
                                        style="width:5px;height:5px;border-radius:50%;background:var(--color-text-muted);">
                                    </div>
                                </div>
                                <span class="text-muted">{{ $check['label'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>
