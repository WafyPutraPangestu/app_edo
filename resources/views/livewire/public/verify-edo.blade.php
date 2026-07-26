<div class="min-h-screen flex flex-col items-center justify-center p-4" style="background-color: var(--color-surface-2);">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden border border-subtle">

        {{-- HEADER LOGO --}}
        <div class="px-6 py-4 flex items-center justify-center gap-3"
            style="background-color: var(--color-surface-1); border-b: 1px solid var(--color-border-medium);">
            <div class="w-8 h-8 rounded flex items-center justify-center text-white font-bold"
                style="background-color: var(--color-primary);">
                NCS
            </div>
            <h1 class="font-bold tracking-widest uppercase text-sm" style="color: var(--color-text-primary);">PT. NCS Line
                World Wide</h1>
        </div>

        <div class="p-6">
            @if ($isValid)
                {{-- STATUS VALID --}}
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4"
                        style="background-color: var(--color-success-subtle); color: var(--color-success); border: 4px solid var(--color-success-border);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black uppercase tracking-wide" style="color: var(--color-success);">Dokumen
                        Asli</h2>
                    <p class="text-sm mt-1" style="color: var(--color-text-muted);">Otentikasi Elektronik Valid & Sah
                    </p>
                </div>

                <div class="space-y-4">
                    {{-- Info Kargo --}}
                    <div class="p-4 rounded-xl"
                        style="background-color: var(--color-surface-2); border: 1px solid var(--color-border-medium);">
                        <span class="block text-xs font-bold uppercase tracking-wider mb-2"
                            style="color: var(--color-text-muted);">Informasi Kargo</span>
                        <div class="grid grid-cols-2 gap-y-3 text-sm">
                            <div>
                                <span class="block text-xs" style="color: var(--color-text-muted);">No. AWB</span>
                                <span class="font-bold font-mono"
                                    style="color: var(--color-text-primary);">{{ $documentData->releaseRequest->awb_number }}</span>
                            </div>
                            <div>
                                <span class="block text-xs" style="color: var(--color-text-muted);">No.
                                    Penerbangan</span>
                                <span class="font-bold"
                                    style="color: var(--color-text-primary);">{{ $documentData->releaseRequest->flight_number }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-xs" style="color: var(--color-text-muted);">Rute (Origin ➔
                                    Dest)</span>
                                <span class="font-bold"
                                    style="color: var(--color-text-primary);">{{ $documentData->releaseRequest->origin }}
                                    ➔ {{ $documentData->releaseRequest->destination }}</span>
                            </div>
                            <div>
                                <span class="block text-xs" style="color: var(--color-text-muted);">Kuantitas</span>
                                <span class="font-bold"
                                    style="color: var(--color-text-primary);">{{ $documentData->releaseRequest->quantity }}</span>
                            </div>
                            <div>
                                <span class="block text-xs" style="color: var(--color-text-muted);">Berat (GW)</span>
                                <span class="font-bold"
                                    style="color: var(--color-text-primary);">{{ $documentData->releaseRequest->gross_weight }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Otorisasi --}}
                    <div class="p-4 rounded-xl"
                        style="background-color: var(--color-blue-subtle); border: 1px solid var(--color-blue-border);">
                        <span class="block text-xs font-bold uppercase tracking-wider mb-2"
                            style="color: var(--color-blue);">Otorisasi Kepemilikan</span>
                        <div class="text-sm space-y-2">
                            <div>
                                <span class="block text-xs" style="color: var(--color-blue);">Klien / Importir</span>
                                <span class="font-bold"
                                    style="color: #000;">{{ $documentData->releaseRequest->user->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs" style="color: var(--color-blue);">Waktu Penerbitan
                                    e-DO</span>
                                <span class="font-bold"
                                    style="color: #000;">{{ \Carbon\Carbon::parse($documentData->issued_date)->translatedFormat('d F Y, H:i') }}
                                    WIB</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 text-center" style="border-top: 1px solid var(--color-border-medium);">
                    <p class="text-xs leading-relaxed" style="color: var(--color-text-muted);">Dokumen ini diterbitkan
                        secara elektronik oleh sistem PT. NCS Line World Wide. Silakan serahkan kargo sesuai dengan
                        rincian di atas.</p>
                </div>
            @else
                {{-- STATUS TIDAK VALID / PALSU --}}
                <div class="flex flex-col items-center text-center py-8">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4"
                        style="background-color: var(--color-danger-subtle); color: var(--color-danger); border: 4px solid var(--color-danger-border);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black uppercase tracking-wide" style="color: var(--color-danger);">DOKUMEN
                        PALSU</h2>
                    <p class="text-sm mt-2" style="color: var(--color-text-secondary);">QR Code ini tidak dikenali atau
                        dokumen telah dipalsukan. <strong style="color: #0000;">JANGAN SERAHKAN
                            KARGO.</strong></p>
                </div>
            @endif
        </div>
    </div>
</div>
