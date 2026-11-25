<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    /**
     * Generate official YKN "BORANG KEBENARAN MENGGUNAKAN KENDERAAN PEJABAT" PDF.
     */
    public function borangKenderaan(Request $request, Booking $booking): Response
    {
        $this->authorizeBookingPdf($request->user(), $booking);

        $booking->load(['applicant', 'vehicle', 'pilot', 'approver']);

        $pdf = Pdf::loadView('pdf.borang_kenderaan', [
            'booking' => $booking,
        ])->setPaper('a4', 'portrait');

        $filename = "Borang-Kenderaan-{$booking->booking_code}.pdf";

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Generate official YKN "MEMO - KEBENARAN MEMANDU KENDERAAN PEJABAT" PDF.
     * Enabled strictly when request_type === 'vehicle_only' and status === 'approved'.
     */
    public function memoMemandu(Request $request, Booking $booking): Response
    {
        $this->authorizeBookingPdf($request->user(), $booking);

        if (! $booking->canDownloadMemo()) {
            abort(403, __('Memo Kebenaran Memandu Kenderaan Pejabat hanya dijana untuk permohonan Kenderaan Sahaja (Pandu Sendiri) yang telah diluluskan.'));
        }

        $booking->load(['applicant', 'vehicle', 'approver']);

        $pdf = Pdf::loadView('pdf.memo_memandu', [
            'booking' => $booking,
        ])->setPaper('a4', 'portrait');

        $filename = "Memo-Kebenaran-Memandu-{$booking->booking_code}.pdf";

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    private function authorizeBookingPdf(User $user, Booking $booking): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isStaff() && $booking->user_id === $user->id) {
            return;
        }

        if ($user->isPilot() && $booking->pilot_id === $user->id) {
            return;
        }

        abort(403, __('Anda tidak dibenarkan memuat turun dokumen PDF bagi tempahan ini.'));
    }
}
