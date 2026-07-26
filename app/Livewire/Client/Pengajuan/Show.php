<?php

namespace App\Livewire\Client\Pengajuan;

use App\Models\ReleaseRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public ReleaseRequest $releaseRequest;

    public function mount($id): void
    {
        $this->releaseRequest = ReleaseRequest::with([
            'requestCharges.localCharge',
            'transaction',
            'releaseDocument',
        ])->findOrFail($id);

        if ($this->releaseRequest->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Ini bukan pengajuan Anda.');
        }
    }

    public function render()
    {
        return view('livewire.client.pengajuan.show');
    }
}
