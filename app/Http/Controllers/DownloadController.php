<?php

namespace App\Http\Controllers;

use App\Models\ReleaseDocument;
use App\Models\ReleaseRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function edo($id)
    {
        $document = ReleaseDocument::with(['releaseRequest.user'])->findOrFail($id);
        if (Auth::user()->role === 'client' && $document->releaseRequest->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak. Ini bukan dokumen Anda.');
        }
        $pdf = Pdf::loadView('pdf.edo-document', compact('document'));
        $pdf->setPaper('A4', 'portrait');
        $namaFile = 'e-DO_NCS_' . $document->releaseRequest->awb_number . '.pdf';
        return $pdf->stream($namaFile);
    }

    public function invoice($id)
    {
        $document = ReleaseDocument::with(['releaseRequest.user', 'releaseRequest.transaction'])->findOrFail($id);

        if (Auth::user()->role === 'client' && $document->releaseRequest->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak. Ini bukan dokumen Anda.');
        }

        $pdf = Pdf::loadView('pdf.invoice-document', compact('document'));
        $pdf->setPaper('A4', 'portrait');

        $namaFile = 'Invoice_NCS_' . $document->releaseRequest->awb_number . '.pdf';

        return $pdf->stream($namaFile);
    }

    public function awb($id)
    {
        $releaseRequest = ReleaseRequest::findOrFail($id);

        if (Auth::user()->role === 'client' && $releaseRequest->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak. Ini bukan dokumen Anda.');
        }

        if (!$releaseRequest->awb_path || !Storage::disk('public')->exists($releaseRequest->awb_path)) {
            abort(404, 'File AWB belum diunggah oleh admin.');
        }

        $namaFile = 'AWB_' . ($releaseRequest->awb_number ?: $releaseRequest->id) . '.pdf';

        return Storage::disk('public')->download($releaseRequest->awb_path, $namaFile);
    }
}
