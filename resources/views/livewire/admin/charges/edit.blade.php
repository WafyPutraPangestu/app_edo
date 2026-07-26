<div x-data="{ tarif: $wire.entangle('tarif') }">
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.charges.index') }}" wire:navigate class="av-icon-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
            <h1>Edit Komponen Biaya</h1>
            <p class="text-muted text-sm mt-1">ID #{{ $chargeId }} — perbarui nama atau nominal tarif.</p>
        </div>
    </div>

    <form wire:submit.prevent="update" class="av-card max-w-lg">
        <div class="av-card-body space-y-4">
            <div>
                <label class="av-label" for="name">Nama Komponen</label>
                <input id="name" type="text" wire:model="name"
                    class="av-input @error('name') av-input--error @enderror">
                @error('name')
                    <p class="av-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="av-label" for="tarif">Nominal Tarif</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-muted text-sm pointer-events-none">Rp</span>
                    <input id="tarif" type="number" min="0" step="1000" x-model="tarif"
                        class="av-input pl-9 @error('tarif') av-input--error @enderror">
                </div>
                @error('tarif')
                    <p class="av-field-error">{{ $message }}</p>
                @enderror
                <p class="av-field-hint">
                    Preview:
                    <span class="text-accent font-medium"
                        x-text="'Rp ' + (tarif ? Number(tarif).toLocaleString('id-ID') : '0')"></span>
                    <span class="block mt-1">
                        Mengubah tarif di sini hanya berlaku untuk tagihan baru — tagihan lama tetap memakai nominal
                        yang sudah tersimpan (snapshot).
                    </span>
                </p>
            </div>
        </div>

        <div class="p-4 border-t border-subtle flex justify-end gap-2">
            <a href="{{ route('admin.charges.index') }}" wire:navigate class="av-btn av-btn--ghost">Batal</a>
            <button type="submit" class="av-btn av-btn--primary" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                <span wire:loading wire:target="update">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
