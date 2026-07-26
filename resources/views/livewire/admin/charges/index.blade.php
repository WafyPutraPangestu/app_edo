<div x-data="{ confirmDelete: null }" @keydown.escape.window="confirmDelete = null">

    {{-- Flash message --}}
    @if (session('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
            class="av-alert av-alert--success mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mt-0.5 shrink-0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span class="flex-1">{{ session('message') }}</span>
            <button @click="show = false" class="text-muted hover:text-primary">&times;</button>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5 gap-3 flex-wrap">
        <div>
            <h1>Master Local Charges</h1>
            <p class="text-muted text-sm mt-1">Kelola komponen biaya lokal (DO Fee, Storage Fee, dll) yang dipakai untuk
                tagihan klien.</p>
        </div>
        <a href="{{ route('admin.charges.create') }}" wire:navigate class="av-btn av-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Tarif
        </a>
    </div>

    {{-- Card --}}
    <div class="av-card">
        <div class="av-card-header">
            <div class="relative w-full max-w-xs">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama komponen biaya..."
                    class="av-input pl-9 pr-8">
                @if ($search)
                    <button wire:click="$set('search', '')"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted hover:text-primary">
                        &times;
                    </button>
                @endif
            </div>
            <span class="av-badge av-badge--blue">{{ $charges->total() }} komponen</span>
        </div>

        <div class="av-card-body !p-0 relative" wire:loading.class="opacity-50" wire:target="search">
            @if ($charges->count())
                <div class="overflow-x-auto">
                    <table class="av-table">
                        <thead>
                            <tr>
                                <th>Nama Komponen</th>
                                <th>Tarif</th>
                                <th>Terakhir Diubah</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($charges as $charge)
                                <tr wire:key="charge-{{ $charge->id }}">
                                    <td class="col-primary">{{ $charge->name }}</td>
                                    <td>
                                        <span class="av-pill av-pill--blue">
                                            Rp {{ number_format($charge->tarif, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>{{ $charge->updated_at->translatedFormat('d M Y, H:i') }}</td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.charges.edit', $charge->id) }}" wire:navigate
                                                data-tooltip="Edit tarif" class="av-icon-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                                    <path
                                                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <button
                                                @click="confirmDelete = { id: {{ $charge->id }}, name: @js($charge->name) }"
                                                data-tooltip="Hapus tarif" class="av-icon-btn hover:!text-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="av-empty">
                    <div class="av-empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="w-10 h-10 mx-auto">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="av-empty-title">Belum ada komponen biaya</div>
                    <p class="text-sm">
                        @if ($search)
                            Tidak ada tarif yang cocok dengan pencarian "{{ $search }}".
                        @else
                            Mulai dengan menambahkan komponen biaya pertama, misalnya DO Fee atau Storage Fee.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        @if ($charges->hasPages())
            <div class="p-4 border-t border-subtle">
                {{ $charges->links() }}
            </div>
        @endif
    </div>

    {{-- Delete Confirm --}}
    <div x-show="confirmDelete" x-cloak x-transition.opacity
        class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6)"
        @click.self="confirmDelete = null">
        <div x-show="confirmDelete" x-transition class="av-card w-full max-w-sm">
            <div class="av-card-body text-center pt-6">
                <div class="av-stat-icon av-stat-icon--red mx-auto !mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path
                            d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <h3 class="mb-1">Hapus komponen ini?</h3>
                <p class="text-sm text-muted">
                    <span class="text-secondary" x-text="confirmDelete?.name"></span>
                    akan dihapus secara permanen. Tarif yang sudah pernah dipakai di tagihan lama tetap tersimpan
                    (snapshot), tapi komponen ini tidak bisa dipilih lagi untuk tagihan baru.
                </p>
            </div>
            <div class="p-4 border-t border-subtle flex justify-end gap-2">
                <button @click="confirmDelete = null" class="av-btn av-btn--ghost av-btn--sm">Batal</button>
                <button @click="$wire.deleteCharge(confirmDelete.id); confirmDelete = null"
                    class="av-btn av-btn--danger av-btn--sm">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
