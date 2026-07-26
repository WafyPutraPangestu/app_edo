<?php

namespace App\Livewire\Admin\Charges;

use App\Models\LocalCharge;
use Livewire\Component;

class Create extends Component
{
    public $name, $tarif;

    protected $rules = [
        'name' => 'required|string|max:255',
        'tarif' => 'required|numeric|min:0',
    ];

    public function save()
    {
        $this->validate();

        LocalCharge::create([
            'name' => $this->name,
            'tarif' => $this->tarif,
        ]);

        session()->flash('message', 'Komponen biaya baru berhasil disimpan.');
        return redirect()->route('admin.charges.index');
    }

    public function render()
    {
        return view('livewire.admin.charges.create');
    }
}
