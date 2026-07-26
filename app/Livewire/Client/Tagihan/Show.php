<?php

namespace App\Livewire\Client\Tagihan;

use App\Models\ReleaseDocument;
use App\Models\ReleaseRequest;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public ReleaseRequest $releaseRequest;
    public Transaction $transaction;
    public string $snapToken = '';

    // Kontrol polling manual (bukan callback Midtrans)
    public bool $polling = false;
    public int $pollAttempts = 0;
    public int $maxPollAttempts = 120; // 120 x 5 detik ≈ 10 menit

    public function mount($id, MidtransService $midtrans): void
    {
        $this->releaseRequest = ReleaseRequest::with(['requestCharges.localCharge', 'transaction'])
            ->findOrFail($id);

        if ($this->releaseRequest->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($this->releaseRequest->status !== 'approved' && $this->releaseRequest->status !== 'completed') {
            abort(403, 'Pengajuan ini belum disetujui admin, tagihan belum tersedia.');
        }

        if ($this->releaseRequest->requestCharges->isEmpty()) {
            abort(403, 'Admin belum menetapkan tagihan untuk pengajuan ini.');
        }

        // Buat transaksi kalau belum ada (idempotent lewat firstOrCreate)
        $this->transaction = $this->releaseRequest->transaction ?? $this->releaseRequest->transaction()->create([
            'user_id'        => Auth::id(),
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'total_amount'   => $this->releaseRequest->requestCharges->sum('amount'),
            'status'         => 'unpaid',
        ]);

        // Kalau sudah lunas, langsung lempar balik ke halaman detail
        if ($this->transaction->status === 'paid') {
            $this->redirectRoute('client.pengajuan.show', $this->releaseRequest->id, navigate: true);

            return;
        }

        $this->transaction->setRelation('releaseRequest', $this->releaseRequest);
        $this->snapToken = $midtrans->getSnapToken($this->transaction);
    }

    /**
     * Dipanggil dari JS (Snap onSuccess/onPending/onClose/onError) untuk
     * mulai polling. Ini HANYA sinyal UX ("mungkin sudah bayar, cek dong"),
     * bukan sumber kebenaran — status asli tetap dari pollStatus() yang
     * query langsung ke Midtrans.
     */
    public function startPolling(): void
    {
        $this->polling      = true;
        $this->pollAttempts = 0;
    }

    /**
     * Dipanggil berkala oleh wire:poll (atau manual lewat tombol "Cek Status").
     * Ini pengganti notification callback Midtrans: server yang aktif
     * bertanya ke Midtrans, bukan menunggu Midtrans mengirim notifikasi.
     */
    public function pollStatus(MidtransService $midtrans): void
    {
        $this->pollAttempts++;

        $result = $midtrans->checkStatus($this->transaction->invoice_number);

        if ($result) {
            $mapped = $midtrans->mapStatus($result);

            if ($mapped !== $this->transaction->status) {
                $this->applyStatus($mapped, $result);
            }

            if ($mapped === 'paid') {
                $this->polling = false;

                return;
            }

            if ($mapped === 'failed') {
                $this->polling = false;

                return;
            }
        }

        if ($this->pollAttempts >= $this->maxPollAttempts) {
            $this->polling = false;
        }
    }

    protected function applyStatus(string $status, object $result): void
    {
        DB::transaction(function () use ($status, $result) {
            $this->transaction->update([
                'status'                   => $status,
                'payment_method'           => $result->payment_type ?? $this->transaction->payment_method,
                'midtrans_transaction_id'  => $result->transaction_id ?? $this->transaction->midtrans_transaction_id,
                'payment_date'             => $status === 'paid' ? now() : $this->transaction->payment_date,
            ]);

            if ($status === 'paid') {
                ReleaseDocument::firstOrCreate(
                    ['release_request_id' => $this->releaseRequest->id],
                    [
                        'qr_code_string' => (string) Str::uuid(),
                        'issued_date'    => now(),
                    ]
                );

                $this->releaseRequest->update(['status' => 'completed']);
            }
        });

        $this->transaction->refresh();
        $this->releaseRequest->refresh();
    }

    public function render()
    {
        return view('livewire.client.tagihan.show');
    }
}
