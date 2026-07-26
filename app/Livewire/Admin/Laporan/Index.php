<?php

namespace App\Livewire\Admin\Laporan;

use App\Models\ReleaseRequest;
use App\Models\Transaction;
use Livewire\Component;
use Carbon\Carbon;

class Index extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Secara default saat halaman dibuka, tampilkan data 30 hari terakhir
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        // 1. REKAPITULASI KEUANGAN: Menghitung total uang masuk (hanya yang statusnya 'paid')
        $totalPendapatan = Transaction::where('status', 'paid')
            ->whereBetween('payment_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->sum('total_amount');

        // 2. REKAPITULASI OPERASIONAL: Menghitung kinerja dokumen
        $baseRequestQuery = ReleaseRequest::whereBetween('created_at', [
            $this->startDate . ' 00:00:00',
            $this->endDate . ' 23:59:59'
        ]);

        $totalPengajuan = (clone $baseRequestQuery)->count();
        $totalApproved = (clone $baseRequestQuery)->where('status', 'approved')->count();
        $totalRejected = (clone $baseRequestQuery)->where('status', 'rejected')->count();

        // 3. DAFTAR TRANSAKSI SUKSES: Untuk ditampilkan di tabel laporan (bisa untuk di-print/export nantinya)
        $transaksiSukses = Transaction::with(['user', 'releaseRequest'])
            ->where('status', 'paid')
            ->whereBetween('payment_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->latest()
            ->get();

        return view('livewire.admin.laporan.index', compact(
            'totalPendapatan',
            'totalPengajuan',
            'totalApproved',
            'totalRejected',
            'transaksiSukses'
        ));
    }
}
