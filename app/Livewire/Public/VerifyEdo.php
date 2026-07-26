<?php

namespace App\Livewire\Public;

use App\Models\ReleaseDocument;
use Livewire\Component;

class VerifyEdo extends Component
{
    public $qrString;
    public $documentData;
    public $isValid = false;

    public function mount($qr_string)
    {
        $this->qrString = $qr_string;

        // Cari dokumen e-DO berdasarkan string kriptografi dari QR Code
        $document = ReleaseDocument::with(['releaseRequest.user'])
            ->where('qr_code_string', $qr_string)
            ->first();

        // Jika dokumen ditemukan, tandai valid
        if ($document) {
            $this->documentData = $document;
            $this->isValid = true;
        }
    }

    public function render()
    {
        // Pastikan menggunakan layout utama (atau layout khusus public jika kamu punya)
        return view('livewire.public.verify-edo');
    }
}
