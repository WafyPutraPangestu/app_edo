<div>
    @if (session('success'))
        <div class="av-alert av-alert--success mb-4">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-6">
        <h1>Ajukan Pelepasan Dokumen</h1>
        <p class="text-muted text-sm mt-1">Lengkapi 3 langkah berikut untuk mengajukan pelepasan dokumen kargo Anda.</p>
    </div>

    {{-- Step indicator --}}
    <div class="flex items-center mb-6">
        @foreach (['Surat Kuasa', 'Detail Kargo', 'Review & Kirim'] as $i => $label)
            @php $n = $i + 1; @endphp
            <div class="flex items-center {{ $n < 3 ? 'flex-1' : '' }}">
                <button type="button" wire:click="goToStep({{ $n }})"
                    class="av-avatar {{ $n > $step ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                    style="{{ $n <= $step ? 'background:var(--color-accent);color:#fff;border-color:transparent;' : '' }}">
                    {{ $n < $step ? '✓' : $n }}
                </button>
                <span
                    class="text-xs ml-2 mr-3 {{ $n === $step ? 'text-primary font-medium' : 'text-muted' }} hidden sm:inline">
                    {{ $label }}
                </span>
                @if ($n < 3)
                    <span class="flex-1 h-px"
                        style="background: {{ $n < $step ? 'var(--color-accent)' : 'var(--color-border)' }};"></span>
                @endif
            </div>
        @endforeach
    </div>

    <div class="av-card">
        <div class="av-card-body">

            {{-- STEP 1: Upload surat kuasa --}}
            @if ($step === 1)
                <div>
                    <h3 class="mb-1">Unggah Surat Kuasa</h3>
                    <p class="text-muted text-xs mb-4">Format PDF, JPG, atau PNG. Maksimal 5MB.</p>

                    <label for="surat_kuasa" class="block cursor-pointer">
                        <div class="av-card"
                            style="border-style:dashed; padding:2rem; text-align:center; background:var(--color-surface-1);">
                            @if ($surat_kuasa)
                                <div class="text-2xl mb-2">📄</div>
                                <p class="text-primary text-sm font-medium">{{ $surat_kuasa->getClientOriginalName() }}
                                </p>
                                <p class="text-muted text-xs mt-1">
                                    {{ number_format($surat_kuasa->getSize() / 1024, 0) }} KB — klik untuk mengganti
                                </p>
                            @else
                                <div class="text-muted text-2xl mb-2">⬆️</div>
                                <p class="text-secondary text-sm">Klik untuk memilih file surat kuasa</p>
                            @endif
                        </div>
                        <input id="surat_kuasa" type="file" wire:model="surat_kuasa" class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png">
                    </label>

                    <div wire:loading wire:target="surat_kuasa" class="text-xs text-accent mt-2">Mengunggah file...
                    </div>

                    @error('surat_kuasa')
                        <div class="av-field-error">{{ $message }}</div>
                    @enderror

                    <div class="flex justify-end mt-6">
                        <button wire:click="nextStep" class="av-btn av-btn--primary">Lanjut →</button>
                    </div>
                </div>
            @endif

            {{-- STEP 2: Detail kargo --}}
            @if ($step === 2)
                <div>
                    <h3 class="mb-1">Detail Kargo</h3>
                    <p class="text-muted text-xs mb-4">
                        Opsional — isi jika Anda sudah punya data AWB. Admin akan memverifikasi &amp; melengkapi data
                        ini saat approval.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="av-label">Nomor AWB</label>
                            <input type="text" wire:model="awb_number" class="av-input" placeholder="205-32481223">
                        </div>
                        <div>
                            <label class="av-label">Nomor Penerbangan</label>
                            <input type="text" wire:model="flight_number" class="av-input" placeholder="NH0871">
                        </div>
                        <div>
                            <label class="av-label">Asal (Origin)</label>
                            <input type="text" wire:model="origin" class="av-input" placeholder="BEIJING">
                        </div>
                        <div>
                            <label class="av-label">Tujuan (Destination)</label>
                            <input type="text" wire:model="destination" class="av-input" placeholder="JAKARTA">
                        </div>
                        <div>
                            <label class="av-label">Jumlah Koli</label>
                            <input type="text" wire:model="quantity" class="av-input"
                                placeholder="31 CTNS / 1 PALLET">
                        </div>
                        <div>
                            <label class="av-label">Berat Kotor</label>
                            <input type="text" wire:model="gross_weight" class="av-input" placeholder="532.0 KG">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="av-label">Deskripsi Barang</label>
                            <textarea wire:model="goods_description" rows="3" class="av-textarea" placeholder="TANTALUM WIRE"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button wire:click="prevStep" class="av-btn av-btn--secondary">← Kembali</button>
                        <button wire:click="nextStep" class="av-btn av-btn--primary">Lanjut →</button>
                    </div>
                </div>
            @endif

            {{-- STEP 3: Review --}}
            @if ($step === 3)
                <div>
                    <h3 class="mb-4">Review Pengajuan</h3>

                    <div class="av-alert av-alert--info mb-4">
                        <span>ℹ️</span>
                        <span>Pastikan data sudah benar. Setelah dikirim, pengajuan akan menunggu verifikasi
                            admin.</span>
                    </div>

                    <div class="text-sm">
                        <div class="flex justify-between py-2 border-b border-subtle">
                            <span class="text-muted">Surat Kuasa</span>
                            <span class="text-primary">{{ $surat_kuasa?->getClientOriginalName() ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-subtle">
                            <span class="text-muted">Nomor AWB</span>
                            <span class="text-primary">{{ $awb_number ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-subtle">
                            <span class="text-muted">Penerbangan</span>
                            <span class="text-primary">{{ $flight_number ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-subtle">
                            <span class="text-muted">Rute</span>
                            <span class="text-primary">{{ $origin ?: '-' }} → {{ $destination ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-subtle">
                            <span class="text-muted">Jumlah / Berat</span>
                            <span class="text-primary">{{ $quantity ?: '-' }} / {{ $gross_weight ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-muted">Deskripsi Barang</span>
                            <span class="text-primary text-right">{{ $goods_description ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button wire:click="prevStep" class="av-btn av-btn--secondary">← Kembali</button>
                        <button wire:click="submit" wire:loading.attr="disabled" class="av-btn av-btn--primary">
                            <span wire:loading.remove wire:target="submit">Kirim Pengajuan</span>
                            <span wire:loading wire:target="submit">Mengirim...</span>
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
