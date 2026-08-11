<?php

namespace App\Livewire\Admin;

use App\Models\ReleaseRequest;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public int $chartMonth;
    public int $chartYear;

    public function mount(): void
    {
        $this->chartMonth = (int) now()->month;
        $this->chartYear  = (int) now()->year;
    }

    public function render()
    {
        // ── Kartu Statistik Utama ──────────────────────────────
        $totalPengajuan   = ReleaseRequest::count();
        $pendingReview    = ReleaseRequest::where('status', 'pending')->count();
        $approvedCount    = ReleaseRequest::where('status', 'approved')->count();
        $rejectedCount    = ReleaseRequest::where('status', 'rejected')->count();

        $totalRevenue  = (float) Transaction::where('status', 'paid')->sum('total_amount');
        $unpaidAmount  = (float) Transaction::where('status', 'unpaid')->sum('total_amount');
        $totalClients  = User::where('role', 'client')->count();

        $revenueThisMonth = (float) Transaction::where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('total_amount');

        $revenueLastMonth = (float) Transaction::where('status', 'paid')
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('total_amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // ── Chart: Pendapatan Harian (bulan & tahun terpilih) ──────
        $startOfMonth = Carbon::create($this->chartYear, $this->chartMonth, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();
        $daysInMonth  = $startOfMonth->daysInMonth;
        $dayNumbers   = collect(range(1, $daysInMonth));

        $rawDailyRevenue = Transaction::where('status', 'paid')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('DAY(payment_date) as d, SUM(total_amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $chartLabels  = $dayNumbers->values();
        $chartRevenue = $dayNumbers->map(fn($d) => (float) ($rawDailyRevenue[$d] ?? 0))->values();

        // ── Chart: Pengajuan Masuk Harian ──────
        $rawDailyRequests = ReleaseRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DAY(created_at) as d, COUNT(*) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $chartRequests = $dayNumbers->map(fn($d) => (int) ($rawDailyRequests[$d] ?? 0))->values();

        $chartMonthName = $startOfMonth->translatedFormat('F Y');

        // ── Distribusi Metode Pembayaran (Bar Chart) ───────────
        $paymentMethods = Transaction::where('status', 'paid')
            ->whereNotNull('payment_method')
            ->selectRaw('payment_method, count(*) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        // ── Aktivitas Terbaru ───────────────────────────────────
        $recentRequests = ReleaseRequest::with('user')->latest()->limit(6)->get();
        $recentTransactions = Transaction::with('user')->latest()->limit(6)->get();

        return view('livewire.admin.dashboard', [
            'totalPengajuan'     => $totalPengajuan,
            'pendingReview'      => $pendingReview,
            'approvedCount'      => $approvedCount,
            'rejectedCount'      => $rejectedCount,
            'totalRevenue'       => $totalRevenue,
            'unpaidAmount'       => $unpaidAmount,
            'totalClients'       => $totalClients,
            'revenueGrowth'      => $revenueGrowth,
            'chartLabels'        => $chartLabels,
            'chartRevenue'       => $chartRevenue,
            'chartRequests'      => $chartRequests,
            'chartMonthName'     => $chartMonthName,
            'paymentMethods'     => $paymentMethods,
            'recentRequests'     => $recentRequests,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
