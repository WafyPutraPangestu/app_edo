<?php

namespace App\Livewire\Admin;

use App\Models\ReleaseRequest;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
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

        // ── Tren Pendapatan 6 Bulan Terakhir (Line Chart) ──────
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $rawRevenue = Transaction::where('status', 'paid')
            ->where('payment_date', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as ym, SUM(total_amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $chartLabels = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values();
        $chartRevenue = $months->map(fn($m) => (float) ($rawRevenue[$m] ?? 0))->values();

        // ── Distribusi Status Pengajuan (Doughnut Chart) ───────
        $statusBreakdown = [
            'Pending'  => $pendingReview,
            'Approved' => $approvedCount,
            'Rejected' => $rejectedCount,
        ];

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
            'totalPengajuan'    => $totalPengajuan,
            'pendingReview'     => $pendingReview,
            'approvedCount'     => $approvedCount,
            'rejectedCount'     => $rejectedCount,
            'totalRevenue'      => $totalRevenue,
            'unpaidAmount'      => $unpaidAmount,
            'totalClients'      => $totalClients,
            'revenueGrowth'     => $revenueGrowth,
            'chartLabels'       => $chartLabels,
            'chartRevenue'      => $chartRevenue,
            'statusBreakdown'   => $statusBreakdown,
            'paymentMethods'    => $paymentMethods,
            'recentRequests'    => $recentRequests,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
