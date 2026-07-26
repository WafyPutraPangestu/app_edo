<?php

namespace App\Livewire\Admin\Riwayat;

use App\Models\Transaction;
use Livewire\Component;

class Show extends Component
{
    public $transaction;

    public function mount($id)
    {
        // Tarik data transaksi beserta relasi lengkap (Klien, Dokumen Pengajuan, dan Rincian Biaya)
        $this->transaction = Transaction::with([
            'user',
            'releaseRequest.requestCharges.localCharge',
            'releaseRequest.releaseDocument' // Jika e-DO sudah terbit, kita bisa tampilkan detail dokumennya
        ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.riwayat.show');
    }
}
