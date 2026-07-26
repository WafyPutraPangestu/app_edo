<?php

namespace App\Livewire\Client;

use App\Models\ReleaseRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        // ── Statistik Ringkas ───────────────────────────────
        $totalPengajuan = ReleaseRequest::where('user_id', $user->id)->count();
        $pendingCount   = ReleaseRequest::where('user_id', $user->id)->where('status', 'pending')->count();
        $approvedCount  = ReleaseRequest::where('user_id', $user->id)->where('status', 'approved')->count();
        $rejectedCount  = ReleaseRequest::where('user_id', $user->id)->where('status', 'rejected')->count();

        // ── Tagihan yang perlu dibayar (paling penting utk client) ──
        $unpaidTransactions = Transaction::with('releaseRequest')
            ->where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->latest()
            ->get();

        $totalUnpaid = (float) $unpaidTransactions->sum('total_amount');

        // ── Total sudah dibayar ─────────────────────────────
        $totalPaid = (float) Transaction::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('total_amount');

        // ── e-DO yang siap diunduh ───────────────────────────
        $readyDocuments = ReleaseRequest::with('releaseDocument', 'transaction')
            ->where('user_id', $user->id)
            ->whereHas('releaseDocument')
            ->latest()
            ->limit(5)
            ->get();

        // ── Pengajuan terbaru (semua status, untuk timeline) ──
        $recentRequests = ReleaseRequest::with('transaction', 'releaseDocument')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        return view('livewire.client.dashboard', [
            'totalPengajuan'      => $totalPengajuan,
            'pendingCount'        => $pendingCount,
            'approvedCount'       => $approvedCount,
            'rejectedCount'       => $rejectedCount,
            'unpaidTransactions'  => $unpaidTransactions,
            'totalUnpaid'         => $totalUnpaid,
            'totalPaid'           => $totalPaid,
            'readyDocuments'      => $readyDocuments,
            'recentRequests'      => $recentRequests,
        ]);
    }
}
