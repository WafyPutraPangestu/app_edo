<?php

namespace App\Livewire\Admin\Client;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Edit extends Component
{
    public $clientId;
    public $name, $email, $password, $company_name, $phone;

    public function mount($id)
    {
        $client = User::query()->where('role', 'client')->findOrFail($id);
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->email = $client->email;
        $this->company_name = $client->company_name;
        $this->phone = $client->phone;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->clientId,
            'password' => 'nullable|string|min:6',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function update()
    {
        $this->validate();

        $client = User::findOrFail($this->clientId);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'phone' => $this->phone,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $client->update($data);

        session()->flash('message', 'Profil klien berhasil diperbarui.');
        return redirect()->route('admin.client.index');
    }

    public function render()
    {
        return view('livewire.admin.client.edit');
    }
}
