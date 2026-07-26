<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.pengajuan.show', $releaseRequest->id) }}" wire:navigate class="av-icon-btn">←</a>
        <div>
            <h1>Pembayaran Tagihan</h1>
            <p class="text-muted text-sm mt-1">Invoice {{ $transaction->invoice_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Rincian --}}
        <div class="lg:col-span-2">
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
                                    <td style="text-align:right">Rp {{ number_format($charge->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="col-primary">Total Tagihan</td>
                                <td class="col-primary" style="text-align:right">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="av-alert av-alert--info mt-4">
                <span>ℹ️</span>
                <span class="text-xs">
                    Aplikasi ini tidak menggunakan notification callback dari Midtrans. Setelah membayar,
                    status akan diverifikasi otomatis dengan mengecek langsung ke Midtrans setiap beberapa detik.
                    Jangan tutup halaman ini sampai status berubah menjadi "Lunas".
                </span>
            </div>
        </div>

        {{-- Status & tombol bayar --}}
        <div>
            <div class="av-card">
                <div class="av-card-header"><span class="av-card-title">Status Pembayaran</span></div>
                <div class="av-card-body space-y-4">

                    @if ($transaction->status === 'unpaid')
                        <div class="av-alert av-alert--warning">
                            <span>⏳</span>
                            <span class="text-xs">Belum dibayar</span>
                        </div>

                        <button type="button" id="btn-pay" class="av-btn av-btn--primary" style="width:100%;"
                            onclick="window.payWithMidtrans()">
                            💳 Bayar Sekarang
                        </button>

                        @if ($polling)
                            <div wire:poll.5000ms="pollStatus"></div>
                            <div class="flex items-center gap-2 text-xs text-muted justify-center mt-2">
                                <span class="av-skeleton"
                                    style="width:10px;height:10px;border-radius:50%;display:inline-block;"></span>
                                Mengecek status pembayaran otomatis...
                            </div>
                        @endif

                        <button wire:click="pollStatus" wire:loading.attr="disabled"
                            class="av-btn av-btn--ghost av-btn--sm" style="width:100%;">
                            🔄 Cek Status Sekarang
                        </button>
                    @elseif ($transaction->status === 'paid')
                        <div class="av-alert av-alert--success">
                            <span>✅</span>
                            <span class="text-xs">Pembayaran berhasil! Mengalihkan ke halaman detail...</span>
                        </div>
                        <script>
                            setTimeout(function() {
                                window.location.href = @js(route('client.pengajuan.show', $releaseRequest->id));
                            }, 1500);
                        </script>
                    @else
                        <div class="av-alert av-alert--danger">
                            <span>❌</span>
                            <span class="text-xs">Pembayaran gagal / dibatalkan. Silakan coba lagi.</span>
                        </div>
                        <button wire:click="$refresh" class="av-btn av-btn--secondary" style="width:100%;">Coba
                            Lagi</button>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://app.{{ config('midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        window.payWithMidtrans = function() {
            if (typeof window.snap === 'undefined') {
                alert('Snap.js belum termuat, coba refresh halaman.');
                return;
            }

            window.snap.pay(@js($snapToken), {
                onSuccess: function() {
                    @this.call('startPolling');
                },
                onPending: function() {
                    @this.call('startPolling');
                },
                onError: function() {
                    @this.call('startPolling');
                },
                onClose: function() {
                    @this.call('startPolling');
                },
            });
        };
    </script>
@endpush
