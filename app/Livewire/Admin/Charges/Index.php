<?php

namespace App\Livewire\Admin\Charges;

use App\Models\LocalCharge;
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

    public function deleteCharge($id)
    {
        LocalCharge::findOrFail($id)->delete();
        session()->flash('message', 'Tarif berhasil dihapus.');
    }

    public function render()
    {
        $charges = LocalCharge::query()->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.charges.index', compact('charges'));
    }
}
