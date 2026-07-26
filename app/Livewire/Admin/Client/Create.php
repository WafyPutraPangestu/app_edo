<?php

namespace App\Livewire\Admin\Client;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public $name, $email, $password, $company_name, $phone;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function save()
    {
        $this->validate();

        User::create([
            'role' => 'client',
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'company_name' => $this->company_name,
            'phone' => $this->phone,
        ]);

        session()->flash('message', 'Klien baru berhasil ditambahkan.');
        return redirect()->route('admin.client.index');
    }

    public function render()
    {
        return view('livewire.admin.client.create');
    }
}
