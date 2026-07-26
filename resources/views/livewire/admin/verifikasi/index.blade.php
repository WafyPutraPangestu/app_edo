<div>
    <div class="mb-6">
        <h1>Verifikasi Pengajuan</h1>
        <p class="text-muted text-sm mt-1">Verifikasi dokumen, tetapkan tagihan, dan kelola AWB — semua dalam satu
            halaman.</p>
    </div>

    @if (session('success'))
        <div class="av-alert av-alert--success mb-4">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" style="align-items:start;">

        {{-- ══════════════ LIST PANEL ══════════════ --}}
        <div class="lg:col-span-4">
            <div class="av-card">
                <div class="av-card-body" style="padding-bottom:0.75rem;">
                    <input type="text" wire:model.live.debounce.400ms="search" class="av-input"
                        placeholder="Cari nama client, perusahaan, atau no. AWB...">
                </div>

                <div class="px-3 pb-3 flex flex-wrap gap-1.5">
                    @php
                        $tabs = [
                            'pending' => ['Perlu Verifikasi', $counts['pending'], 'amber'],
                            'approved_unpaid' => ['Menunggu Bayar', $counts['approved_unpaid'], 'blue'],
                            'awaiting_awb' => ['Menunggu AWB', $counts['awaiting_awb'], 'blue'],
                            'done' => ['Selesai', $counts['done'], 'green'],
                            'rejected' => ['Ditolak', $counts['rejected'], 'red'],
                            'all' => ['Semua', $counts['all'], 'gray'],
                        ];
                    @endphp
                    @foreach ($tabs as $key => [$label, $count, $color])
                        <button type="button" wire:click="$set('listFilter', '{{ $key }}')"
                            class="av-badge {{ $listFilter === $key ? 'av-badge--' . $color : 'av-badge--gray' }}"
                            style="cursor:pointer; border-width:1px; {{ $listFilter === $key ? '' : 'opacity:.6;' }}">
                            {{ $label }} · {{ $count }}
                        </button>
                    @endforeach
                </div>

                <div class="av-divider" style="margin:0;"></div>

                <div style="max-height: 640px; overflow-y:auto;">
                    @forelse ($list as $req)
                        @php
                            $statusMap = [
                                'pending' => ['amber', 'Perlu Verifikasi'],
                                'approved' => ['blue', 'Menunggu Bayar'],
                                'rejected' => ['red', 'Ditolak'],
                                'completed' => $req->awb_path ? ['green', 'Selesai'] : ['blue', 'Menunggu AWB'],
                            ];
                            [$rc, $rl] = $statusMap[$req->status] ?? ['gray', $req->status];
                        @endphp
                        <button type="button" wire:click="selectRequest({{ $req->id }})"
                            wire:key="list-{{ $req->id }}" class="w-full text-left px-4 py-3"
                            style="border-bottom:1px solid var(--color-border); background:{{ $selectedId === $req->id ? 'var(--color-accent-subtle)' : 'transparent' }}; border-left: 2px solid {{ $selectedId === $req->id ? 'var(--color-accent)' : 'transparent' }};">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="text-primary text-sm font-medium truncate">{{ $req->user->company_name ?: $req->user->name }}</span>
                                <span class="av-pill av-pill--{{ $rc }}"
                                    style="flex-shrink:0;">{{ $rl }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-muted">
                                <span>{{ $req->awb_number ?: 'Belum ada AWB' }}</span>
                                <span>{{ $req->created_at->format('d M Y') }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="av-empty">
                            <div class="av-empty-icon">📭</div>
                            <div class="av-empty-title">Tidak ada data</div>
                        </div>
                    @endforelse
                </div>

                @if ($list->hasPages())
                    <div class="px-4 py-3 border-t border-subtle">
                        {{ $list->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════════ DETAIL PANEL ══════════════ --}}
        <div class="lg:col-span-8">
            @if (!$selected)
                <div class="av-card">
                    <div class="av-empty">
                        <div class="av-empty-icon">👈</div>
                        <div class="av-empty-title">Pilih pengajuan dari daftar</div>
                        <p class="text-xs">Detail, tagihan, dan aksi verifikasi akan tampil di sini.</p>
                    </div>
                </div>
            @else
                @php
                    $statusMap = [
                        'pending' => ['amber', 'Perlu Verifikasi'],
                        'approved' => ['blue', 'Menunggu Bayar'],
                        'rejected' => ['red', 'Ditolak'],
                        'completed' => $selected->awb_path ? ['green', 'Selesai'] : ['blue', 'Menunggu AWB'],
                    ];
                    [$sc, $sl] = $statusMap[$selected->status] ?? ['gray', $selected->status];
                @endphp

                <div class="space-y-6">

                    {{-- Header --}}
                    <div class="av-card">
                        <div class="av-card-body flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3>#{{ $selected->id }} —
                                        {{ $selected->user->company_name ?: $selected->user->name }}</h3>
                                    <span class="av-pill av-pill--{{ $sc }}">{{ $sl }}</span>
                                </div>
                                <p class="text-muted text-xs">
                                    {{ $selected->user->name }} · {{ $selected->user->email }}
                                    @if ($selected->user->phone)
                                        · {{ $selected->user->phone }}
                                    @endif
                                </p>
                                <p class="text-muted text-xs mt-1">Diajukan
                                    {{ $selected->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $selected->surat_kuasa_path) }}" target="_blank"
                                class="av-btn av-btn--secondary av-btn--sm">
                                📄 Surat Kuasa
                            </a>
                        </div>
                    </div>

                    {{-- Rejection note --}}
                    @if ($selected->status === 'rejected' && $selected->rejection_note)
                        <div class="av-alert av-alert--danger">
                            <span>⚠️</span>
                            <div class="flex-1">
                                <strong class="block mb-1">Alasan Penolakan</strong>
                                {{ $selected->rejection_note }}
                            </div>
                        </div>
                        <div>
                            <button wire:click="reopen" class="av-btn av-btn--secondary">
                                ↺ Verifikasi Ulang
                            </button>
                        </div>
                    @endif

                    {{-- Detail Kargo + Tagihan (form) --}}
                    @if ($selected->status !== 'rejected')
                        <div class="av-card">
                            <div class="av-card-header">
                                <span class="av-card-title">Detail Kargo</span>
                                @if (!$this->chargesEditable)
                                    <span class="av-badge av-badge--gray">Terkunci — tagihan sudah dibuka client</span>
                                @endif
                            </div>
                            <div class="av-card-body">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="av-label">Nomor AWB</label>
                                        <input type="text" wire:model="awb_number" class="av-input"
                                            placeholder="205-32481223" @disabled(!$this->chargesEditable)>
                                        @error('awb_number')
                                            <div class="av-field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="av-label">Nomor Penerbangan</label>
                                        <input type="text" wire:model="flight_number" class="av-input"
                                            placeholder="NH0871" @disabled(!$this->chargesEditable)>
                                        @error('flight_number')
                                            <div class="av-field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="av-label">Asal (Origin)</label>
                                        <input type="text" wire:model="origin" class="av-input" placeholder="BEIJING"
                                            @disabled(!$this->chargesEditable)>
                                        @error('origin')
                                            <div class="av-field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="av-label">Tujuan (Destination)</label>
                                        <input type="text" wire:model="destination" class="av-input"
                                            placeholder="JAKARTA" @disabled(!$this->chargesEditable)>
                                        @error('destination')
                                            <div class="av-field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="av-label">Jumlah Koli</label>
                                        <input type="text" wire:model="quantity" class="av-input"
                                            placeholder="31 CTNS / 1 PALLET" @disabled(!$this->chargesEditable)>
                                        @error('quantity')
                                            <div class="av-field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="av-label">Berat Kotor</label>
                                        <input type="text" wire:model="gross_weight" class="av-input"
                                            placeholder="532.0 KG" @disabled(!$this->chargesEditable)>
                                        @error('gross_weight')
                                            <div class="av-field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="av-label">Deskripsi Barang</label>
                                        <textarea wire:model="goods_description" rows="2" class="av-textarea" placeholder="TANTALUM WIRE"
                                            @disabled(!$this->chargesEditable)></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="av-card">
                            <div class="av-card-header">
                                <span class="av-card-title">Tagihan (Local Charges)</span>
                            </div>
                            <div class="av-card-body">
                                @if ($this->chargesEditable)
                                    <div class="space-y-2">
                                        @foreach ($charges as $i => $row)
                                            <div class="flex items-center gap-2"
                                                wire:key="charge-row-{{ $i }}">
                                                <select wire:model="charges.{{ $i }}.local_charge_id"
                                                    class="av-select" style="flex:2;">
                                                    <option value="">— Pilih komponen —</option>
                                                    @foreach ($this->availableLocalCharges as $lc)
                                                        <option value="{{ $lc->id }}">{{ $lc->name }} (Rp
                                                            {{ number_format($lc->tarif, 0, ',', '.') }})</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" step="0.01" min="0"
                                                    wire:model="charges.{{ $i }}.amount" class="av-input"
                                                    style="flex:1;" placeholder="Nominal">
                                                <button type="button"
                                                    wire:click="removeChargeRow({{ $i }})"
                                                    class="av-icon-btn" title="Hapus baris">✕</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('charges')
                                        <div class="av-field-error mt-2">{{ $message }}</div>
                                    @enderror

                                    <button type="button" wire:click="addChargeRow"
                                        class="av-btn av-btn--ghost av-btn--sm mt-3">
                                        + Tambah Komponen
                                    </button>

                                    <div class="av-divider"></div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-muted text-sm">Total Tagihan</span>
                                        <span class="text-primary text-lg font-medium">Rp
                                            {{ number_format($this->chargesTotal, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <table class="av-table">
                                        <thead>
                                            <tr>
                                                <th>Komponen</th>
                                                <th style="text-align:right">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selected->requestCharges as $charge)
                                                <tr>
                                                    <td>{{ $charge->localCharge->name }}</td>
                                                    <td style="text-align:right">Rp
                                                        {{ number_format($charge->amount, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="col-primary">Total</td>
                                                <td class="col-primary" style="text-align:right">
                                                    Rp
                                                    {{ number_format($selected->requestCharges->sum('amount'), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Reject inline form --}}
                    @if ($selected->status === 'pending' && $showRejectForm)
                        <div class="av-card">
                            <div class="av-card-header"><span class="av-card-title">Tolak Pengajuan</span></div>
                            <div class="av-card-body">
                                <label class="av-label">Alasan Penolakan</label>
                                <textarea wire:model="rejection_note" rows="3" class="av-textarea"
                                    placeholder="Jelaskan alasan penolakan agar client bisa memperbaiki..."></textarea>
                                @error('rejection_note')
                                    <div class="av-field-error">{{ $message }}</div>
                                @enderror
                                <div class="flex justify-end gap-2 mt-3">
                                    <button wire:click="toggleRejectForm"
                                        class="av-btn av-btn--secondary av-btn--sm">Batal</button>
                                    <button wire:click="reject" class="av-btn av-btn--danger av-btn--sm">Tolak
                                        Pengajuan</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action bar sesuai tahap --}}
                    @if ($selected->status === 'pending' && !$showRejectForm)
                        <div class="flex justify-end gap-2">
                            <button wire:click="toggleRejectForm" class="av-btn av-btn--danger">Tolak</button>
                            <button wire:click="saveVerification" wire:loading.attr="disabled"
                                class="av-btn av-btn--primary">
                                <span wire:loading.remove wire:target="saveVerification">✅ Setujui & Terbitkan
                                    Tagihan</span>
                                <span wire:loading wire:target="saveVerification">Menyimpan...</span>
                            </button>
                        </div>
                    @elseif ($selected->status === 'approved' && $this->chargesEditable)
                        <div class="av-alert av-alert--info">
                            <span>ℹ️</span>
                            <span class="text-xs">Sudah disetujui, menunggu client membuka halaman pembayaran. Tagihan
                                masih bisa diubah.</span>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="saveVerification" wire:loading.attr="disabled"
                                class="av-btn av-btn--primary">
                                <span wire:loading.remove wire:target="saveVerification">Simpan Perubahan
                                    Tagihan</span>
                                <span wire:loading wire:target="saveVerification">Menyimpan...</span>
                            </button>
                        </div>
                    @elseif ($selected->status === 'approved')
                        <div class="av-alert av-alert--warning">
                            <span>⏳</span>
                            <span class="text-xs">
                                Menunggu pembayaran dari client — Invoice {{ $selected->transaction->invoice_number }}
                                (Rp {{ number_format($selected->transaction->total_amount, 0, ',', '.') }}).
                            </span>
                        </div>
                    @elseif ($selected->status === 'completed' && !$selected->awb_path)
                        <div class="av-card">
                            <div class="av-card-header"><span class="av-card-title">Upload AWB</span></div>
                            <div class="av-card-body">
                                <div class="av-alert av-alert--success mb-4">
                                    <span>✅</span>
                                    <span class="text-xs">Pembayaran lunas. e-DO &amp; Invoice sudah bisa diunduh
                                        client. Unggah AWB untuk menyelesaikan proses.</span>
                                </div>

                                <label class="av-label">File AWB (PDF/JPG/PNG, maks 5MB)</label>
                                <input type="file" wire:model="awb_file" accept=".pdf,.jpg,.jpeg,.png"
                                    class="av-input">
                                <div wire:loading wire:target="awb_file" class="text-xs text-accent mt-1">
                                    Mengunggah...</div>
                                @error('awb_file')
                                    <div class="av-field-error">{{ $message }}</div>
                                @enderror

                                <div class="flex justify-end mt-4">
                                    <button wire:click="uploadAwb" wire:loading.attr="disabled"
                                        class="av-btn av-btn--primary">
                                        <span wire:loading.remove wire:target="uploadAwb">📤 Upload AWB</span>
                                        <span wire:loading wire:target="uploadAwb">Mengunggah...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif ($selected->status === 'completed' && $selected->awb_path)
                        <div class="av-card">
                            <div class="av-card-header"><span class="av-card-title">Dokumen Selesai</span></div>
                            <div class="av-card-body space-y-3">
                                <div class="av-alert av-alert--success">
                                    <span>✅</span>
                                    <span class="text-xs">Seluruh proses selesai. Client dapat mengunduh e-DO, Invoice,
                                        dan AWB.</span>
                                </div>
                                <a href="{{ asset('storage/' . $selected->awb_path) }}" target="_blank"
                                    class="av-btn av-btn--secondary" style="width:100%;">
                                    ✈️ Lihat File AWB Terunggah
                                </a>

                                <div class="av-divider"></div>
                                <label class="av-label">Ganti File AWB</label>
                                <input type="file" wire:model="awb_file" accept=".pdf,.jpg,.jpeg,.png"
                                    class="av-input">
                                @error('awb_file')
                                    <div class="av-field-error">{{ $message }}</div>
                                @enderror
                                <button wire:click="uploadAwb" wire:loading.attr="disabled"
                                    class="av-btn av-btn--ghost av-btn--sm mt-2">
                                    Ganti File
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            @endif
        </div>
    </div>
</div>
