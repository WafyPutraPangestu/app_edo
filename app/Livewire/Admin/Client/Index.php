<?php

namespace App\Livewire\Admin\Client;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    // State untuk Modal Detail
    public $selectedClient = null;
    public $showModal = false;

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showDetail($id)
    {
        $this->selectedClient = User::query()->where('role', 'client')->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedClient = null;
    }

    public function deleteClient($id)
    {
        $client = User::query()->where('role', 'client')->findOrFail($id);
        $client->delete('*');

        session()->flash('message', 'Data klien berhasil dihapus.');
    }

    public function render()
    {
        $clients = User::query()->where('role', 'client')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('company_name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.client.index', compact('clients'));
    }
}
