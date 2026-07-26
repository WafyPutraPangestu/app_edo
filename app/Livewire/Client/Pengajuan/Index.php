<?php

namespace App\Livewire\Client\Pengajuan;

use App\Models\ReleaseRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = '';

    public string $sort = 'latest';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();

        $requests = ReleaseRequest::query()
            ->where('user_id', $userId)
            ->with(['transaction', 'releaseDocument'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('awb_number', 'like', "%{$this->search}%")
                        ->orWhere('flight_number', 'like', "%{$this->search}%")
                        ->orWhere('goods_description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('created_at', $this->sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(10);

        $stats = [
            'total'     => ReleaseRequest::where('user_id', $userId)->count(),
            'pending'   => ReleaseRequest::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved'  => ReleaseRequest::where('user_id', $userId)->where('status', 'approved')->count(),
            'completed' => ReleaseRequest::where('user_id', $userId)->where('status', 'completed')->count(),
        ];

        return view('livewire.client.pengajuan.index', compact('requests', 'stats'));
    }
}
