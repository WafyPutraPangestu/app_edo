<?php

namespace App\Livewire\Admin\Charges;

use App\Models\LocalCharge;
use Livewire\Component;

class Edit extends Component
{
    public $chargeId, $name, $tarif;

    public function mount($id)
    {
        $charge = LocalCharge::findOrFail($id);
        $this->chargeId = $charge->id;
        $this->name = $charge->name;
        $this->tarif = $charge->tarif;
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'tarif' => 'required|numeric|min:0',
    ];

    public function update()
    {
        $this->validate();

        $charge = LocalCharge::findOrFail($this->chargeId);
        $charge->update([
            'name' => $this->name,
            'tarif' => $this->tarif,
        ]);

        session()->flash('message', 'Komponen biaya berhasil diubah.');
        return redirect()->route('admin.charges.index');
    }

    public function render()
    {
        return view('livewire.admin.charges.edit');
    }
}
