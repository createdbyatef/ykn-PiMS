<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly BookingAvailabilityService $availabilityService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isPilot()) {
            return redirect()->route('pilot.trips.index');
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->staffDashboard($user);
    }

    private function adminDashboard(): View
    {
        $now = now();

        $pendingCount = Booking::where('status', Booking::STATUS_PENDING)->count();
        $urgentPendingCount = Booking::where('status', Booking::STATUS_PENDING)->where('is_urgent', true)->count();

        $activeTripsCount = Booking::where('status', Booking::STATUS_APPROVED)
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>=', $now)
            ->count();

        $upcomingApprovedCount = Booking::where('status', Booking::STATUS_APPROVED)
            ->where('start_datetime', '>', $now)
            ->count();

        $availablePilots = $this->availabilityService->getAvailablePilotsNow();
        $totalActivePilots = User::pilots()->active()->count();

        $maintenanceVehiclesCount = Vehicle::where('status', Vehicle::STATUS_MAINTENANCE)->count();
        $totalVehiclesCount = Vehicle::count();

        $pendingBookings = Booking::with(['applicant', 'vehicle', 'pilot'])
            ->where('status', Booking::STATUS_PENDING)
            ->orderByDesc('is_urgent')
            ->orderBy('start_datetime')
            ->limit(8)
            ->get();

        $activeAndUpcomingTrips = Booking::with(['applicant', 'vehicle', 'pilot'])
            ->where('status', Booking::STATUS_APPROVED)
            ->where('end_datetime', '>=', $now)
            ->orderBy('start_datetime')
            ->limit(6)
            ->get();

        $vehicles = Vehicle::orderBy('plate_number')->get();
        $pilots = User::pilots()->active()->orderBy('name')->get();
        $availablePilotIds = $availablePilots->pluck('id')->all();
        $availableVehicleIds = $this->availabilityService->getAvailableVehiclesNow()->pluck('id')->all();

        return view('dashboard.admin', compact(
            'pendingCount',
            'urgentPendingCount',
            'activeTripsCount',
            'upcomingApprovedCount',
            'availablePilots',
            'totalActivePilots',
            'maintenanceVehiclesCount',
            'totalVehiclesCount',
            'pendingBookings',
            'activeAndUpcomingTrips',
            'vehicles',
            'pilots',
            'availablePilotIds',
            'availableVehicleIds'
        ));
    }

    private function staffDashboard(User $user): View
    {
        $stats = [
            'pending' => Booking::where('user_id', $user->id)->where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::where('user_id', $user->id)->where('status', Booking::STATUS_APPROVED)->count(),
            'completed' => Booking::where('user_id', $user->id)->where('status', Booking::STATUS_COMPLETED)->count(),
            'rejected' => Booking::where('user_id', $user->id)->where('status', Booking::STATUS_REJECTED)->count(),
        ];

        $recentBookings = Booking::with(['vehicle', 'pilot', 'approver'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        $availableVehiclesCount = $this->availabilityService->getAvailableVehiclesNow()->count();
        $totalVehiclesCount = Vehicle::where('status', '!=', Vehicle::STATUS_INACTIVE)->count();
        $availablePilotsCount = $this->availabilityService->getAvailablePilotsNow()->count();
        $totalPilotsCount = User::pilots()->active()->count();

        return view('dashboard.staff', compact(
            'stats',
            'recentBookings',
            'availableVehiclesCount',
            'totalVehiclesCount',
            'availablePilotsCount',
            'totalPilotsCount'
        ));
    }
}
