<div x-data="{ showPassword: false }">
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.client.index') }}" wire:navigate class="av-icon-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
            <h1>Edit Klien</h1>
            <p class="text-muted text-sm mt-1">ID #{{ $clientId }} — perbarui data akun klien.</p>
        </div>
    </div>

    <form wire:submit.prevent="update" class="av-card max-w-2xl">
        <div class="av-card-body space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="av-label" for="name">Nama Lengkap</label>
                    <input id="name" type="text" wire:model="name"
                        class="av-input @error('name') av-input--error @enderror">
                    @error('name')
                        <p class="av-field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="av-label" for="email">Email</label>
                    <input id="email" type="email" wire:model="email"
                        class="av-input @error('email') av-input--error @enderror">
                    @error('email')
                        <p class="av-field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="av-label" for="password">Password Baru</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input :type="showPassword ? 'text' : 'password'" id="password" wire:model="password"
                            class="av-input pr-9 @error('password') av-input--error @enderror"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted hover:text-primary">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="av-btn av-btn--secondary"
                        @click="
                            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
                            let pass = '';
                            for (let i = 0; i < 10; i++) pass += chars[Math.floor(Math.random() * chars.length)];
                            $wire.password = pass;
                            showPassword = true;
                        ">
                        Generate
                    </button>
                </div>
                @error('password')
                    <p class="av-field-error">{{ $message }}</p>
                @enderror
                <p class="av-field-hint">Kosongkan kolom ini jika password klien tidak perlu diubah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="av-label" for="company_name">Nama Perusahaan</label>
                    <input id="company_name" type="text" wire:model="company_name"
                        class="av-input @error('company_name') av-input--error @enderror">
                    @error('company_name')
                        <p class="av-field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="av-label" for="phone">No. Telepon</label>
                    <input id="phone" type="text" wire:model="phone"
                        class="av-input @error('phone') av-input--error @enderror">
                    @error('phone')
                        <p class="av-field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-subtle flex justify-end gap-2">
            <a href="{{ route('admin.client.index') }}" wire:navigate class="av-btn av-btn--ghost">Batal</a>
            <button type="submit" class="av-btn av-btn--primary" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                <span wire:loading wire:target="update">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
