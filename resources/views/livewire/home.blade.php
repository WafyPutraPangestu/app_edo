<div class="bg-base">

    {{-- ============================================================
         HERO SECTION
    ============================================================ --}}
    <section x-data="{ mx: 50, my: 50 }"
        @mousemove.window="mx = ($event.clientX / window.innerWidth) * 100; my = ($event.clientY / window.innerHeight) * 100"
        class="relative overflow-hidden border-b border-subtle">
        {{-- glow yang mengikuti kursor --}}
        <div class="pointer-events-none absolute inset-0 transition-all duration-300"
            :style="`background: radial-gradient(600px circle at ${mx}% ${my}%, rgba(37,99,235,0.14), transparent 45%)`">
        </div>

        {{-- garis radar dekoratif --}}
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full border border-accent-border/40"
            style="border-color: var(--color-accent-border)"></div>
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full opacity-30"
            style="border: 1px dashed var(--color-steel)"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-20 lg:py-28">
            <div class="flex flex-col items-start gap-6 max-w-2xl">
                <span class="av-badge av-badge--blue">
                    ✈️ PT. NCS Line World Wide
                </span>

                <h1 class="text-3xl lg:text-4xl font-medium text-primary leading-tight">
                    Pelepasan Kargo Impor,
                    <span class="text-accent">Sepenuhnya Digital.</span>
                </h1>

                <p class="text-secondary text-sm lg:text-base leading-relaxed">
                    AEROIMPORT memangkas birokrasi manual pengurusan Delivery Order.
                    Unggah dokumen, bayar tagihan local charges lewat Midtrans, dan
                    unduh e-DO ber-QR code — tanpa perlu datang ke kantor.
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('login') }}" class="av-btn av-btn--primary av-btn--lg">
                        Masuk ke Akun
                    </a>
                    <a href="#alur" class="av-btn av-btn--ghost av-btn--lg">
                        Lihat Cara Kerja
                    </a>
                </div>

                <div class="flex items-center gap-2 pt-4 text-xs text-muted">
                    <span class="av-pill av-pill--green">Live</span>
                    Sistem verifikasi &amp; pembayaran berjalan real-time
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         STAT STRIP (angka berjalan pakai Alpine)
    ============================================================ --}}
    <section class="border-b border-subtle bg-surface-1" x-data="{
        shown: false,
        stats: [
            { label: 'Pengajuan Diproses', target: 1240, current: 0, suffix: '+' },
            { label: 'Rata-rata Verifikasi', target: 15, current: 0, suffix: ' Menit' },
            { label: 'e-DO Diterbitkan', target: 980, current: 0, suffix: '+' },
            { label: 'Uptime Sistem', target: 99, current: 0, suffix: '.9%' },
        ],
        run() {
            if (this.shown) return;
            this.shown = true;
            this.stats.forEach(s => {
                let step = Math.max(1, Math.ceil(s.target / 60));
                let iv = setInterval(() => {
                    s.current = Math.min(s.target, s.current + step);
                    if (s.current >= s.target) clearInterval(iv);
                }, 20);
            });
        }
    }" x-init="setTimeout(() => run(), 250)">
        <div class="max-w-6xl mx-auto px-6 py-8 av-grid-stats">
            <template x-for="(s, i) in stats" :key="i">
                <div class="av-stat-card">
                    <div class="av-stat-value" x-text="s.current + s.suffix"></div>
                    <div class="av-stat-label" x-text="s.label"></div>
                </div>
            </template>
        </div>
    </section>

    {{-- ============================================================
         ALUR / FLOW SECTION (stepper interaktif Alpine)
    ============================================================ --}}
    <section id="alur" class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center max-w-xl mx-auto mb-12">
            <span class="av-badge av-badge--amber mb-3">Alur Proses</span>
            <h2 class="text-2xl font-medium text-primary mt-3">4 Langkah Menuju e-DO</h2>
            <p class="text-secondary text-sm mt-2">
                Dari unggah dokumen sampai kargo bisa diambil di gudang, semua tercatat dan otomatis.
            </p>
        </div>

        <div x-data="{
            active: 0,
            steps: [
                { icon: '📤', title: 'Klien Unggah Berkas', desc: 'Klien mengisi nomor AWB dan mengunggah Surat Kuasa serta AWB dalam format PDF/Gambar.' },
                { icon: '🔍', title: 'Admin Verifikasi & Tagihan', desc: 'Admin memeriksa keabsahan dokumen, menyetujui berkas, lalu menginput nominal local charges.' },
                { icon: '💳', title: 'Klien Bayar via Midtrans', desc: 'Klien menekan \u201cBayar Sekarang\u201d, lalu membayar via QRIS, Virtual Account, atau ShopeePay.' },
                { icon: '📄', title: 'e-DO Terbit Otomatis', desc: 'Setelah pembayaran diverifikasi webhook Midtrans, sistem menerbitkan PDF e-DO ber-QR code.' },
            ]
        }" class="grid lg:grid-cols-4 gap-4">
            <template x-for="(step, i) in steps" :key="i">
                <button type="button" @mouseenter="active = i" @click="active = i"
                    class="av-card text-left p-5 transition-all duration-200 cursor-pointer"
                    :class="active === i ? 'ring-1' : ''"
                    :style="active === i ? 'border-color: var(--color-accent-border); background: var(--color-surface-3)' : ''">
                    <div class="flex items-center justify-between mb-4">
                        <div class="av-stat-icon av-stat-icon--blue text-lg" x-text="step.icon"></div>
                        <span class="text-xs text-muted">0<span x-text="i + 1"></span></span>
                    </div>
                    <h3 class="text-sm font-medium text-primary mb-2" x-text="step.title"></h3>
                    <p class="text-xs text-secondary leading-relaxed" x-text="step.desc"></p>
                </button>
            </template>
        </div>
    </section>

    {{-- ============================================================
         ROLE SHOWCASE (tab Klien vs Admin)
    ============================================================ --}}
    <section class="border-y border-subtle bg-surface-1">
        <div class="max-w-6xl mx-auto px-6 py-20" x-data="{ tab: 'klien' }">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10">
                <div>
                    <span class="av-badge av-badge--blue mb-3">Hak Akses</span>
                    <h2 class="text-2xl font-medium text-primary mt-3">Dibuat untuk Dua Peran</h2>
                </div>

                <div class="inline-flex p-1 rounded-lg bg-surface-2 border border-subtle w-fit">
                    <button type="button" @click="tab = 'klien'" class="av-btn av-btn--sm"
                        :class="tab === 'klien' ? 'av-btn--primary' : 'av-btn--ghost'" style="border:none">
                        Klien (Importir)
                    </button>
                    <button type="button" @click="tab = 'admin'" class="av-btn av-btn--sm"
                        :class="tab === 'admin' ? 'av-btn--primary' : 'av-btn--ghost'" style="border:none">
                        Admin (Ops &amp; Finance)
                    </button>
                </div>
            </div>

            {{-- panel Klien --}}
            <div x-show="tab === 'klien'" x-cloak x-transition.opacity class="grid md:grid-cols-2 gap-4">
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--blue mb-3">📋</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Ajukan Release Kargo</h3>
                    <p class="text-xs text-secondary">Isi nomor AWB dan unggah Surat Kuasa langsung dari dashboard.</p>
                </div>
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--green mb-3">🧾</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Pantau Tagihan</h3>
                    <p class="text-xs text-secondary">Lihat rincian local charges begitu Admin selesai memverifikasi.
                    </p>
                </div>
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--amber mb-3">💳</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Bayar Sekali Klik</h3>
                    <p class="text-xs text-secondary">Pembayaran via Midtrans Snap: QRIS, VA Bank, hingga e-wallet.</p>
                </div>
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--blue mb-3">📥</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Unduh e-DO</h3>
                    <p class="text-xs text-secondary">Dokumen pelepasan ber-QR code langsung tersedia setelah lunas.</p>
                </div>
            </div>

            {{-- panel Admin --}}
            <div x-show="tab === 'admin'" x-cloak x-transition.opacity class="grid md:grid-cols-2 gap-4">
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--amber mb-3">🔎</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Verifikasi Berkas</h3>
                    <p class="text-xs text-secondary">Periksa keabsahan Surat Kuasa dan AWB sebelum menyetujui
                        pengajuan.</p>
                </div>
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--blue mb-3">💰</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Input Local Charges</h3>
                    <p class="text-xs text-secondary">Susun nominal tagihan per komponen biaya secara terstruktur.</p>
                </div>
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--green mb-3">📊</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Riwayat Pembayaran</h3>
                    <p class="text-xs text-secondary">Status transaksi terupdate otomatis lewat webhook Midtrans.</p>
                </div>
                <div class="av-card av-card-body">
                    <div class="av-stat-icon av-stat-icon--amber mb-3">🖨️</div>
                    <h3 class="text-sm font-medium text-primary mb-1">Terbitkan e-DO</h3>
                    <p class="text-xs text-secondary">PDF beserta QR code dibuat otomatis begitu status Lunas.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         FAQ ACCORDION
    ============================================================ --}}
    <section class="max-w-3xl mx-auto px-6 py-20">
        <div class="text-center mb-10">
            <span class="av-badge av-badge--gray mb-3">FAQ</span>
            <h2 class="text-2xl font-medium text-primary mt-3">Pertanyaan Umum</h2>
        </div>

        <div x-data="{
            open: 0,
            faqs: [
                { q: 'Apakah saya perlu datang ke kantor untuk mengambil e-DO?', a: 'Tidak. Setelah pembayaran lunas, e-DO ber-QR code bisa langsung diunduh dan dibawa ke gudang bandara.' },
                { q: 'Metode pembayaran apa saja yang didukung?', a: 'Semua metode yang tersedia di Midtrans Snap, termasuk QRIS, Virtual Account bank, dan e-wallet seperti ShopeePay.' },
                { q: 'Bagaimana jika dokumen saya ditolak Admin?', a: 'Anda akan menerima catatan penolakan (rejection note) di dashboard dan dapat mengunggah ulang berkas yang sesuai.' },
                { q: 'Apakah status pembayaran diperbarui otomatis?', a: 'Ya. Notifikasi webhook dari Midtrans memperbarui status transaksi secara real-time tanpa perlu pengecekan manual.' },
            ]
        }" class="space-y-3">
            <template x-for="(faq, i) in faqs" :key="i">
                <div class="av-card overflow-hidden">
                    <button type="button" @click="open = open === i ? -1 : i"
                        class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left cursor-pointer">
                        <span class="text-sm font-medium text-primary" x-text="faq.q"></span>
                        <span class="text-muted transition-transform duration-200 shrink-0"
                            :class="open === i ? 'rotate-45' : ''">＋</span>
                    </button>
                    <div x-show="open === i" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <p class="px-5 pb-4 text-xs text-secondary leading-relaxed" x-text="faq.a"></p>
                    </div>
                </div>
            </template>
        </div>
    </section>

    {{-- ============================================================
         CTA FOOTER BAND
    ============================================================ --}}
    <section class="border-t border-subtle">
        <div
            class="max-w-6xl mx-auto px-6 py-16 flex flex-col lg:flex-row items-center justify-between gap-6 rounded-2xl">
            <div>
                <h2 class="text-xl font-medium text-primary mb-1">Siap mempercepat proses release kargo Anda?</h2>
                <p class="text-sm text-secondary">Masuk dengan akun yang telah dibuatkan Admin untuk mulai mengajukan.
                </p>
            </div>
            <a href="{{ route('login') }}" class="av-btn av-btn--primary av-btn--lg shrink-0">
                Masuk Sekarang →
            </a>
        </div>
    </section>

</div>
