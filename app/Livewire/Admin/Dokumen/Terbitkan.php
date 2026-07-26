<?php

namespace App\Livewire\Admin\Dokumen;

use App\Models\ReleaseRequest;
use App\Models\ReleaseDocument;
use Illuminate\Support\Str;
use Livewire\Component;

class Terbitkan extends Component
{
    public $requestId;
    public $requestData;

    public function mount($id)
    {
        $this->requestId = $id;
        $this->requestData = ReleaseRequest::with(['user', 'transaction', 'releaseDocument'])->findOrFail($id);

        // Keamanan: Tolak jika status belum approved atau belum bayar
        if ($this->requestData->status !== 'approved' || ($this->requestData->transaction && $this->requestData->transaction->status !== 'paid')) {
            session()->flash('error', 'Akses ditolak. Pengajuan belum disetujui atau tagihan belum lunas.');
            return redirect()->route('admin.dokumen.index');
        }
    }

    public function generateEdo()
    {
        // Proteksi agar tidak membuat dokumen ganda
        if ($this->requestData->releaseDocument) {
            session()->flash('error', 'Dokumen e-DO untuk AWB ini sudah pernah diterbitkan.');
            return;
        }

        // Generate String unik kriptografi untuk QR Code (Sesuai landasan teori skripsi)
        // Kita menggunakan UUID dipadukan dengan ID unik transaksi
        $qrString = 'EDO-' . $this->requestData->awb_number . '-' . strtoupper(Str::random(10));

        ReleaseDocument::create([
            'release_request_id' => $this->requestId,
            'qr_code_string' => $qrString,
            'issued_date' => now(),
            // pdf_path akan diisi nanti jika menggunakan library PDF generator seperti DomPDF
        ]);

        // Refresh data agar UI langsung berubah (Reactive)
        $this->requestData->load('releaseDocument');

        session()->flash('message', 'Sukses! Dokumen e-DO elektronik beserta QR Code berhasil di-generate.');
    }

    public function render()
    {
        return view('livewire.admin.dokumen.terbitkan');
    }
}
