<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        private readonly BookingAvailabilityService $availabilityService
    ) {}

    public function index(): View
    {
        $vehicles = Vehicle::orderBy('plate_number')->get();
        $pilots = User::pilots()->active()->orderBy('name')->get();
        $availableVehicleIds = $this->availabilityService->getAvailableVehiclesNow()->pluck('id')->all();
        $availablePilotIds = $this->availabilityService->getAvailablePilotsNow()->pluck('id')->all();

        return view('calendar.index', compact(
            'vehicles',
            'pilots',
            'availableVehicleIds',
            'availablePilotIds'
        ));
    }

    public function events(Request $request): JsonResponse
    {
        $user = $request->user();
        $start = $request->query('start') ? Carbon::parse($request->query('start')) : now()->startOfMonth()->subWeek();
        $end = $request->query('end') ? Carbon::parse($request->query('end')) : now()->endOfMonth()->addWeek();

        $query = Booking::with(['applicant', 'vehicle', 'pilot'])
            ->where('start_datetime', '<=', $end)
            ->where('end_datetime', '>=', $start);

        // Visibility rules: Approved & Completed are public internally for availability planning.
        // Pending bookings are visible to Admins and the submitting applicant.
        if ($user->isAdmin()) {
            $query->whereIn('status', [
                Booking::STATUS_APPROVED,
                Booking::STATUS_COMPLETED,
                Booking::STATUS_PENDING,
            ]);
        } elseif ($user->isPilot()) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED])
                    ->orWhere('pilot_id', $user->id);
            });
        } else {
            $query->where(function ($q) use ($user) {
                $q->whereIn('status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED])
                    ->orWhere(function ($sub) use ($user) {
                        $sub->where('status', Booking::STATUS_PENDING)
                            ->where('user_id', $user->id);
                    });
            });
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', (int) $request->query('vehicle_id'));
        }

        if ($request->filled('pilot_id')) {
            $query->where('pilot_id', (int) $request->query('pilot_id'));
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->query('request_type'));
        }

        $bookings = $query->orderBy('start_datetime')->get();

        $events = $bookings->map(function (Booking $booking) use ($user) {
            [$bgColor, $borderColor, $textColor] = match (true) {
                $booking->status === Booking::STATUS_PENDING => ['#fff5e3', '#f5a623', '#8a5400'],
                $booking->status === Booking::STATUS_COMPLETED => ['#e8f6f0', '#1f9d6b', '#146b49'],
                $booking->request_type === Booking::TYPE_VEHICLE_ONLY => ['#a855d6', '#7e36aa', '#ffffff'],
                $booking->request_type === Booking::TYPE_PILOT_ONLY => ['#e0910d', '#b87306', '#ffffff'],
                default => ['#7b6fb5', '#54498c', '#ffffff'],
            };

            $resourceSummary = [];
            if ($booking->vehicle) {
                $resourceSummary[] = $booking->vehicle->plate_number;
            }
            if ($booking->pilot) {
                $parts = explode(' ', $booking->pilot->name);
                $shortPilot = $parts[1] ?? $booking->pilot->name;
                $resourceSummary[] = 'Pilot: '.$shortPilot;
            }
            if (empty($resourceSummary)) {
                $resourceSummary[] = $booking->requestTypeLabel();
            }

            $canViewDetails = $user->isAdmin()
                || $booking->user_id === $user->id
                || ($user->isPilot() && $booking->pilot_id === $user->id);

            return [
                'id' => $booking->id,
                'title' => implode(' • ', $resourceSummary).' — '.$booking->destination,
                'start' => $booking->start_datetime->toIso8601String(),
                'end' => $booking->end_datetime->toIso8601String(),
                'backgroundColor' => $bgColor,
                'borderColor' => $borderColor,
                'textColor' => $textColor,
                'extendedProps' => [
                    'booking_code' => $booking->booking_code,
                    'applicant' => $booking->applicant->name,
                    'unit' => $booking->applicant->unit,
                    'request_type' => $booking->requestTypeLabel(),
                    'vehicle' => $booking->vehicle ? $booking->vehicle->displayName() : 'Tiada / Belum Ditugaskan',
                    'pilot' => $booking->pilot ? $booking->pilot->name : ($booking->isSelfDrive() ? 'Pandu Sendiri (Self-Drive)' : 'Tiada / Belum Ditugaskan'),
                    'destination' => $booking->destination,
                    'purpose' => $booking->purpose,
                    'status' => $booking->status,
                    'status_label' => $booking->statusLabel(),
                    'is_urgent' => $booking->is_urgent,
                    'start_formatted' => $booking->start_datetime->format('d/m/Y h:i A'),
                    'end_formatted' => $booking->end_datetime->format('d/m/Y h:i A'),
                    'detail_url' => $canViewDetails ? route('bookings.show', $booking) : null,
                ],
            ];
        });

        return response()->json($events);
    }
}
