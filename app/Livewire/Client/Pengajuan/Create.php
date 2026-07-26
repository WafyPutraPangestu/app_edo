<?php

namespace App\Livewire\Client\Pengajuan;

use App\Models\ReleaseRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1 — Dokumen
    public $surat_kuasa;

    // Step 2 — Detail kargo (opsional, admin melengkapi/mengoreksi saat verifikasi)
    public ?string $awb_number = null;
    public ?string $flight_number = null;
    public ?string $origin = null;
    public ?string $destination = null;
    public ?string $quantity = null;
    public ?string $gross_weight = null;
    public ?string $goods_description = null;

    protected function rules(): array
    {
        return [
            'surat_kuasa'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'awb_number'         => 'nullable|string|max:100',
            'flight_number'      => 'nullable|string|max:50',
            'origin'             => 'nullable|string|max:100',
            'destination'        => 'nullable|string|max:100',
            'quantity'           => 'nullable|string|max:100',
            'gross_weight'       => 'nullable|string|max:50',
            'goods_description'  => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'surat_kuasa.required' => 'Surat kuasa wajib diunggah.',
        'surat_kuasa.mimes'    => 'Format file harus PDF, JPG, atau PNG.',
        'surat_kuasa.max'      => 'Ukuran file maksimal 5MB.',
    ];

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate(['surat_kuasa' => $this->rules()['surat_kuasa']]);
        }

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        // Hanya boleh loncat mundur, tidak boleh skip ke depan tanpa validasi
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    public function submit(): void
    {
        $validated = $this->validate();

        $path = $validated['surat_kuasa']->store('surat_kuasa', 'public');

        $request = ReleaseRequest::create([
            'user_id'            => Auth::id(),
            'surat_kuasa_path'   => $path,
            'awb_number'         => $this->awb_number,
            'flight_number'      => $this->flight_number,
            'origin'             => $this->origin,
            'destination'        => $this->destination,
            'quantity'           => $this->quantity,
            'gross_weight'       => $this->gross_weight,
            'goods_description'  => $this->goods_description,
            'status'             => 'pending',
        ]);

        session()->flash('success', 'Pengajuan berhasil dikirim. Menunggu verifikasi admin.');

        $this->redirectRoute('client.pengajuan.show', $request->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.client.pengajuan.create');
    }
}
