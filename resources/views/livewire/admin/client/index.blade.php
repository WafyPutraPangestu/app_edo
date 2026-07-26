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
            <h1>Manajemen Klien</h1>
            <p class="text-muted text-sm mt-1">Kelola data akun klien (importir) yang terdaftar di sistem.</p>
        </div>
        <a href="{{ route('admin.client.create') }}" wire:navigate class="av-btn av-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Klien
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
                <input type="text" wire:model.live.debounce.400ms="search"
                    placeholder="Cari nama, email, atau perusahaan..." class="av-input pl-9 pr-8">
                @if ($search)
                    <button wire:click="$set('search', '')"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted hover:text-primary">
                        &times;
                    </button>
                @endif
            </div>
            <span class="av-badge av-badge--blue">{{ $clients->total() }} klien</span>
        </div>

        <div class="av-card-body !p-0 relative" wire:loading.class="opacity-50" wire:target="search">
            @if ($clients->count())
                <div class="overflow-x-auto">
                    <table class="av-table">
                        <thead>
                            <tr>
                                <th>Klien</th>
                                <th>Kontak</th>
                                <th>Perusahaan</th>
                                <th>Terdaftar</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                @php
                                    $initials = collect(explode(' ', $client->name))
                                        ->map(fn($w) => mb_substr($w, 0, 1))
                                        ->take(2)
                                        ->implode('');
                                @endphp
                                <tr wire:key="client-{{ $client->id }}">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="av-avatar">{{ strtoupper($initials) }}</span>
                                            <span class="col-primary">{{ $client->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span>{{ $client->email }}</span>
                                            <span class="text-muted text-xs">{{ $client->phone ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $client->company_name ?? '—' }}</td>
                                    <td>{{ $client->created_at->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button wire:click="showDetail({{ $client->id }})"
                                                data-tooltip="Lihat detail" class="av-icon-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                            <a href="{{ route('admin.client.edit', $client->id) }}" wire:navigate
                                                data-tooltip="Edit klien" class="av-icon-btn">
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
                                                @click="confirmDelete = { id: {{ $client->id }}, name: @js($client->name) }"
                                                data-tooltip="Hapus klien" class="av-icon-btn hover:!text-danger">
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
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="av-empty-title">Belum ada klien</div>
                    <p class="text-sm">
                        @if ($search)
                            Tidak ada klien yang cocok dengan pencarian "{{ $search }}".
                        @else
                            Mulai dengan menambahkan akun klien pertama.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        @if ($clients->hasPages())
            <div class="p-4 border-t border-subtle">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    <div x-show="$wire.showModal" x-cloak x-transition.opacity
        class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6)"
        @click.self="$wire.closeModal()">
        <div x-show="$wire.showModal" x-transition class="av-card w-full max-w-md">
            @if ($selectedClient)
                <div class="av-card-header">
                    <span class="av-card-title">Detail Klien</span>
                    <button @click="$wire.closeModal()" class="av-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="av-card-body space-y-3">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="av-avatar !w-12 !h-12 !text-base">
                            {{ strtoupper(mb_substr($selectedClient->name, 0, 2)) }}
                        </span>
                        <div>
                            <div class="text-primary font-medium">{{ $selectedClient->name }}</div>
                            <div class="text-muted text-xs">ID #{{ $selectedClient->id }}</div>
                        </div>
                    </div>
                    <div class="av-divider"></div>
                    <div class="grid grid-cols-[100px_1fr] gap-y-2 text-sm">
                        <span class="text-muted">Email</span>
                        <span class="text-secondary">{{ $selectedClient->email }}</span>
                        <span class="text-muted">Telepon</span>
                        <span class="text-secondary">{{ $selectedClient->phone ?? '—' }}</span>
                        <span class="text-muted">Perusahaan</span>
                        <span class="text-secondary">{{ $selectedClient->company_name ?? '—' }}</span>
                        <span class="text-muted">Terdaftar</span>
                        <span
                            class="text-secondary">{{ $selectedClient->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div class="p-4 border-t border-subtle flex justify-end gap-2">
                    <a href="{{ route('admin.client.edit', $selectedClient->id) }}" wire:navigate
                        class="av-btn av-btn--secondary av-btn--sm">
                        Edit
                    </a>
                    <button @click="$wire.closeModal()" class="av-btn av-btn--primary av-btn--sm">Tutup</button>
                </div>
            @endif
        </div>
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
                <h3 class="mb-1">Hapus klien ini?</h3>
                <p class="text-sm text-muted">
                    <span class="text-secondary" x-text="confirmDelete?.name"></span>
                    akan dihapus secara permanen beserta seluruh data terkait. Tindakan ini tidak bisa dibatalkan.
                </p>
            </div>
            <div class="p-4 border-t border-subtle flex justify-end gap-2">
                <button @click="confirmDelete = null" class="av-btn av-btn--ghost av-btn--sm">Batal</button>
                <button @click="$wire.deleteClient(confirmDelete.id); confirmDelete = null"
                    class="av-btn av-btn--danger av-btn--sm">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
