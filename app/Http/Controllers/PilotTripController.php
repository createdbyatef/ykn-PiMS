<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PilotTripController extends Controller
{
    public function index(Request $request): View
    {
        $pilot = $request->user();
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayTrips = Booking::with(['applicant', 'vehicle'])
            ->where('pilot_id', $pilot->id)
            ->where('status', Booking::STATUS_APPROVED)
            ->where('start_datetime', '<=', $todayEnd)
            ->where('end_datetime', '>=', $todayStart)
            ->orderBy('start_datetime')
            ->get();

        $upcomingTrips = Booking::with(['applicant', 'vehicle'])
            ->where('pilot_id', $pilot->id)
            ->where('status', Booking::STATUS_APPROVED)
            ->where('start_datetime', '>', $todayEnd)
            ->orderBy('start_datetime')
            ->get();

        $overdueTrips = Booking::with(['applicant', 'vehicle'])
            ->where('pilot_id', $pilot->id)
            ->where('status', Booking::STATUS_APPROVED)
            ->where('end_datetime', '<', $todayStart)
            ->orderBy('start_datetime')
            ->get();

        $completedTrips = Booking::with(['applicant', 'vehicle'])
            ->where('pilot_id', $pilot->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->latest('updated_at')
            ->limit(20)
            ->get();

        $totalDistanceKm = Booking::where('pilot_id', $pilot->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->whereNotNull('odometer_start')
            ->whereNotNull('odometer_end')
            ->get()
            ->sum(fn (Booking $b) => $b->totalDistance() ?? 0);

        return view('pilot.trips', compact(
            'todayTrips',
            'upcomingTrips',
            'overdueTrips',
            'completedTrips',
            'totalDistanceKm'
        ));
    }
}
