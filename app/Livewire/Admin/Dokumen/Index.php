<?php

namespace App\Livewire\Admin\Dokumen;

use App\Models\ReleaseRequest;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Hanya tampilkan pengajuan yang sudah disetujui DAN sudah lunas pembayarannya
        $requests = ReleaseRequest::with(['user', 'transaction', 'releaseDocument'])
            ->where('status', 'approved')
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'paid');
            })
            ->when($this->search, function ($query) {
                $query->where('awb_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.dokumen.index', compact('requests'));
    }
}
