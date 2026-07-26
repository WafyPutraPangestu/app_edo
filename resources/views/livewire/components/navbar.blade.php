@php
    $navLinks = collect();

    $navLinks->push([
        'label' => 'Home',
        'url' => '/',
        'active' => request()->is('/'),
    ]);
@endphp

@can('admin')
    @php
        $navLinks->push([
            'label' => 'Dashboard',
            'url' => route('admin.dashboard'),
            'active' => request()->routeIs('admin.dashboard'),
        ]);
        $navLinks->push([
            'label' => 'Manajemen Klien',
            'url' => route('admin.client.index'),
            'active' => request()->routeIs('admin.client.*'),
        ]);
        $navLinks->push([
            'label' => 'Master Local Charges',
            'url' => route('admin.charges.index'),
            'active' => request()->routeIs('admin.charges.*'),
        ]);
        $navLinks->push([
            'label' => 'Verifikasi Permohonan',
            'url' => route('admin.verifikasi.index'),
            'active' => request()->routeIs('admin.verifikasi.*'),
        ]);
        $navLinks->push([
            'label' => 'Riwayat Transaksi',
            'url' => route('admin.riwayat.index'),
            'active' => request()->routeIs('admin.riwayat.*'),
        ]);
        $navLinks->push([
            'label' => 'Laporan Operasional',
            'url' => route('admin.laporan.index'),
            'active' => request()->routeIs('admin.laporan.*'),
        ]);

    @endphp
@endcan

@can('client')
    @php
        $navLinks->push([
            'label' => 'Dashboard',
            'url' => route('client.dashboard'),
            'active' => request()->routeIs('client.dashboard'),
        ]);
        $navLinks->push([
            'label' => 'Buat Permohonan Baru',
            'url' => route('client.pengajuan.index'),
            'active' => request()->routeIs('client.pengajuan.*'),
        ]);
        // $navLinks->push(['label' => 'Dokumen & Tagihan', 'url' => '#', 'active' => false]);
    @endphp
@endcan

<div x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false" class="relative">
    <nav class="av-navbar">
        {{-- Brand --}}
        <a href="/" wire:navigate class="av-navbar-brand">
            <span class="av-navbar-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M22 2L11 13"></path>
                    <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                </svg>
            </span>
            <span class="flex flex-col leading-none">
                <span class="av-navbar-title">PT NCS Line World Wide</span>
                <span class="av-navbar-sub">Import & Logistics</span>
            </span>
        </a>

        {{-- Desktop nav --}}
        <ul class="av-navbar-links hidden md:flex">
            @foreach ($navLinks as $link)
                <li>
                    <a href="{{ $link['url'] }}" wire:navigate
                        class="av-navbar-link {{ $link['active'] ? 'active' : '' }}">
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Right actions --}}
        <div class="av-navbar-actions">
            @guest
                <a href="{{ route('login') }}" wire:navigate
                    class="av-btn av-btn--primary av-btn--sm px-4 py-4 hidden md:inline-flex">
                    Login
                </a>
            @endguest

            @auth
                <form wire:submit.prevent="logout" class="hidden md:block">
                    <button type="submit" class="av-btn av-btn--ghost av-btn--sm">
                        Logout
                    </button>
                </form>
            @endauth

            {{-- Mobile toggle --}}
            <button @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Toggle menu"
                class="av-icon-btn md:hidden">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
                <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </nav>

    {{-- Mobile dropdown panel --}}
    <div x-show="mobileOpen" x-cloak x-transition @click.outside="mobileOpen = false"
        class="av-navbar-mobile-panel md:hidden">
        <ul class="flex flex-col gap-1 p-3">
            @foreach ($navLinks as $link)
                <li>
                    <a href="{{ $link['url'] }}" wire:navigate @click="mobileOpen = false"
                        class="av-navbar-link {{ $link['active'] ? 'active' : '' }} w-full">
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="p-3 border-t border-subtle flex flex-col gap-2">
            @guest
                <a href="{{ route('login') }}" wire:navigate class="av-btn av-btn--primary justify-center w-full">
                    Login
                </a>
            @endguest

            @auth
                <form wire:submit.prevent="logout" class="w-full">
                    <button type="submit" class="av-btn av-btn--ghost justify-center w-full">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>
