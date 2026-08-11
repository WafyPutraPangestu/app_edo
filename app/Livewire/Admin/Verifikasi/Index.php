<?php

namespace App\Livewire\Admin\Verifikasi;

use App\Models\LocalCharge;
use App\Models\ReleaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    // ── List / filter ──
    public string $listFilter = 'pending'; // all | pending | approved_unpaid | awaiting_awb | done | rejected
    public string $search = '';

    // ── Selected request ──
    public ?int $selectedId = null;
    public ?ReleaseRequest $selected = null;

    // ── Form: detail kargo (dipakai saat verifikasi) ──
    public ?string $invoice_number = null;
    public ?string $awb_number = null;
    public ?string $flight_number = null;
    public ?string $origin = null;
    public ?string $destination = null;
    public ?string $quantity = null;
    public ?string $gross_weight = null;
    public ?string $goods_description = null;

    // ── Form: tagihan ──
    /** @var array<int, array{local_charge_id: string, amount: string}> */
    public array $charges = [];

    // ── Form: penolakan ──
    public string $rejection_note = '';
    public bool $showRejectForm = false;

    // ── Form: upload AWB ──
    public $awb_file = null;

    public function mount(): void
    {
        $allowed = ['all', 'pending', 'approved_unpaid', 'awaiting_awb', 'done', 'rejected'];
        $filter = request()->query('filter');

        if ($filter && in_array($filter, $allowed)) {
            $this->listFilter = $filter;
        }

        $this->resetChargeRows();
    }

    public function updatingListFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getAvailableLocalChargesProperty()
    {
        return LocalCharge::orderBy('name')->get();
    }

    public function getChargesTotalProperty(): float
    {
        return collect($this->charges)->sum(fn($row) => (float) ($row['amount'] ?: 0));
    }

    /**
     * Tagihan boleh diedit selama belum ada Transaction yang dibuat
     * (yaitu client belum pernah membuka halaman pembayaran). Setelah
     * Transaction ada, order_id + gross_amount di Midtrans sudah terkunci,
     * jadi tagihan tidak boleh diutak-atik lagi dari sini.
     */
    public function getChargesEditableProperty(): bool
    {
        if (!$this->selected) {
            return false;
        }

        return in_array($this->selected->status, ['pending', 'approved']) 
            && (!$this->selected->transaction || empty($this->selected->transaction->snap_token));
    }

    public function selectRequest(int $id): void
    {
        $this->selectedId = $id;
        $this->showRejectForm = false;
        $this->loadSelected();
    }

    public function closeDetail(): void
    {
        $this->selectedId = null;
        $this->selected = null;
    }

    protected function loadSelected(): void
    {
        $this->selected = ReleaseRequest::with(['user', 'transaction', 'requestCharges.localCharge', 'releaseDocument'])
            ->find($this->selectedId);

        if (!$this->selected) {
            $this->selectedId = null;

            return;
        }

        $this->awb_number = $this->selected->awb_number;
        $this->flight_number = $this->selected->flight_number;
        $this->origin = $this->selected->origin;

        if ($this->selected->transaction) {
            $this->invoice_number = $this->selected->transaction->invoice_number;
        } else {
            $this->invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        }
        $this->destination = $this->selected->destination;
        $this->quantity = $this->selected->quantity;
        $this->gross_weight = $this->selected->gross_weight;
        $this->goods_description = $this->selected->goods_description;
        $this->rejection_note = '';
        $this->awb_file = null;

        if ($this->selected->requestCharges->isNotEmpty()) {
            $this->charges = $this->selected->requestCharges
                ->map(fn($c) => [
                    'local_charge_id' => (string) $c->local_charge_id,
                    'amount' => (string) $c->amount,
                ])
                ->toArray();
        } else {
            $this->resetChargeRows();
        }
    }

    protected function resetChargeRows(): void
    {
        $this->charges = [['local_charge_id' => '', 'amount' => '']];
    }

    public function addChargeRow(): void
    {
        $this->charges[] = ['local_charge_id' => '', 'amount' => ''];
    }

    public function removeChargeRow(int $index): void
    {
        unset($this->charges[$index]);
        $this->charges = array_values($this->charges);

        if (empty($this->charges)) {
            $this->resetChargeRows();
        }
    }

    /**
     * Simpan/koreksi detail kargo + tagihan, sekaligus set status jadi
     * 'approved' kalau sebelumnya masih pending. Tombol yang sama dipakai
     * baik untuk "Setujui" pertama kali maupun "Update Tagihan" berikutnya.
     */
    public function saveVerification(): void
    {
        if (!$this->selected) {
            return;
        }

        $transactionId = $this->selected->transaction ? $this->selected->transaction->id : 'NULL';

        $this->validate([
            'invoice_number' => 'required|string|max:100|unique:transactions,invoice_number,' . $transactionId,
            'awb_number' => 'required|string|max:100',
            'flight_number' => 'required|string|max:50',
            'origin' => 'required|string|max:100',
            'destination' => 'required|string|max:100',
            'quantity' => 'required|string|max:100',
            'gross_weight' => 'required|string|max:50',
            'goods_description' => 'nullable|string|max:1000',
            'charges' => 'required|array|min:1',
            'charges.*.local_charge_id' => 'required|exists:local_charges,id',
            'charges.*.amount' => 'required|numeric|min:0',
        ], [], [
            'invoice_number' => 'No. Invoice',
            'awb_number' => 'Nomor AWB',
            'flight_number' => 'Nomor penerbangan',
            'origin' => 'Asal',
            'destination' => 'Tujuan',
            'quantity' => 'Jumlah koli',
            'gross_weight' => 'Berat kotor',
            'charges.*.local_charge_id' => 'Komponen biaya',
            'charges.*.amount' => 'Nominal',
        ]);

        DB::transaction(function () {
            $this->selected->update([
                'awb_number' => $this->awb_number,
                'flight_number' => $this->flight_number,
                'origin' => $this->origin,
                'destination' => $this->destination,
                'quantity' => $this->quantity,
                'gross_weight' => $this->gross_weight,
                'goods_description' => $this->goods_description,
                'status' => 'approved',
                'rejection_note' => null,
            ]);

            $this->selected->requestCharges()->delete();

            foreach ($this->charges as $row) {
                $this->selected->requestCharges()->create([
                    'local_charge_id' => $row['local_charge_id'],
                    'amount' => $row['amount'],
                ]);
            }

            $totalAmount = collect($this->charges)->sum('amount');
            if ($this->selected->transaction) {
                $this->selected->transaction->update([
                    'invoice_number' => $this->invoice_number,
                    'total_amount' => $totalAmount,
                    'snap_token' => null,
                ]);
            } else {
                $this->selected->transaction()->create([
                    'user_id' => $this->selected->user_id,
                    'invoice_number' => $this->invoice_number,
                    'total_amount' => $totalAmount,
                    'status' => 'unpaid',
                ]);
            }
        });

        session()->flash('success', 'Verifikasi & tagihan berhasil disimpan.');
        $this->loadSelected();
    }

    public function toggleRejectForm(): void
    {
        $this->showRejectForm = !$this->showRejectForm;
    }

    public function reject(): void
    {
        if (!$this->selected) {
            return;
        }

        $this->validate([
            'rejection_note' => 'required|string|min:5|max:1000',
        ], [], ['rejection_note' => 'Alasan penolakan']);

        $this->selected->update([
            'status' => 'rejected',
            'rejection_note' => $this->rejection_note,
        ]);

        session()->flash('success', 'Pengajuan ditolak.');
        $this->showRejectForm = false;
        $this->loadSelected();
    }

    public function reopen(): void
    {
        if (!$this->selected) {
            return;
        }

        $this->selected->update([
            'status' => 'pending',
            'rejection_note' => null,
        ]);

        session()->flash('success', 'Pengajuan dibuka kembali untuk verifikasi ulang.');
        $this->loadSelected();
    }

    public function uploadAwb(): void
    {
        if (!$this->selected) {
            return;
        }

        $this->validate([
            'awb_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [], ['awb_file' => 'File AWB']);

        $path = $this->awb_file->store('awb', 'public');

        if ($this->selected->awb_path && Storage::disk('public')->exists($this->selected->awb_path)) {
            Storage::disk('public')->delete($this->selected->awb_path);
        }

        $this->selected->update(['awb_path' => $path]);
        $this->awb_file = null;

        session()->flash('success', 'AWB berhasil diunggah. Client sekarang bisa mengunduhnya.');
        $this->loadSelected();
    }

    public function render()
    {
        $counts = [
            'all' => ReleaseRequest::count(),
            'pending' => ReleaseRequest::where('status', 'pending')->count(),
            'approved_unpaid' => ReleaseRequest::where('status', 'approved')->count(),
            'awaiting_awb' => ReleaseRequest::where('status', 'completed')->whereNull('awb_path')->count(),
            'done' => ReleaseRequest::where('status', 'completed')->whereNotNull('awb_path')->count(),
            'rejected' => ReleaseRequest::where('status', 'rejected')->count(),
        ];

        $list = ReleaseRequest::query()
            ->with(['user', 'transaction'])
            ->when($this->listFilter === 'pending', fn($q) => $q->where('status', 'pending'))
            ->when($this->listFilter === 'approved_unpaid', fn($q) => $q->where('status', 'approved'))
            ->when($this->listFilter === 'awaiting_awb', fn($q) => $q->where('status', 'completed')->whereNull('awb_path'))
            ->when($this->listFilter === 'done', fn($q) => $q->where('status', 'completed')->whereNotNull('awb_path'))
            ->when($this->listFilter === 'rejected', fn($q) => $q->where('status', 'rejected'))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('awb_number', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($u) {
                            $u->where('name', 'like', "%{$this->search}%")
                                ->orWhere('company_name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.verifikasi.index', compact('list', 'counts'));
    }
}
