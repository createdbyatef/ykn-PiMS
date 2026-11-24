<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveBookingRequest;
use App\Http\Requests\CompleteTripRequest;
use App\Http\Requests\RejectBookingRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
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

        $query = Booking::with(['applicant', 'vehicle', 'pilot', 'approver'])
            ->latest();

        if ($user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->string('request_type'));
        }

        if ($request->boolean('urgent')) {
            $query->where('is_urgent', true);
        }

        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->trim().'%';
            $query->where(function ($q) use ($term) {
                $q->where('booking_code', 'like', $term)
                    ->orWhere('destination', 'like', $term)
                    ->orWhere('purpose', 'like', $term)
                    ->orWhereHas('applicant', fn ($u) => $u->where('name', 'like', $term)->orWhere('unit', 'like', $term));
            });
        }

        $bookings = $query->paginate(12)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $vehicles = Vehicle::available()->orderBy('plate_number')->get();
        $pilots = User::pilots()->active()->orderBy('name')->get();

        return view('bookings.create', compact('vehicles', 'pilots'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $start = Carbon::parse($validated['start_datetime']);
        $end = Carbon::parse($validated['end_datetime']);

        $isUrgent = $this->availabilityService->isUrgentSubmission($start);

        $booking = Booking::create([
            'booking_code' => Booking::generateBookingCode((int) $start->format('Y')),
            'user_id' => $request->user()->id,
            'request_type' => $validated['request_type'],
            'start_datetime' => $start,
            'end_datetime' => $end,
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            'driver_license_no' => $validated['request_type'] === Booking::TYPE_VEHICLE_ONLY
                ? $validated['driver_license_no']
                : null,
            'is_urgent' => $isUrgent,
            'status' => Booking::STATUS_PENDING,
        ]);

        $message = "Permohonan {$booking->booking_code} telah berjaya dihantar ke Unit Pentadbiran.";
        if ($isUrgent) {
            $message .= ' (Ditandakan sebagai SEGERA kerana notis kurang daripada 3 hari).';
        }

        return redirect()->route('bookings.show', $booking)->with('success', $message);
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeView($request->user(), $booking);

        $booking->load(['applicant', 'vehicle', 'pilot', 'approver']);

        $vehiclesAvailability = collect();
        $pilotsAvailability = collect();

        if ($request->user()->isAdmin() && $booking->isPending()) {
            $vehiclesAvailability = $this->availabilityService->getVehiclesWithAvailability(
                $booking->start_datetime,
                $booking->end_datetime,
                $booking->id
            );

            $pilotsAvailability = $this->availabilityService->getPilotsWithAvailability(
                $booking->start_datetime,
                $booking->end_datetime,
                $booking->id
            );
        }

        return view('bookings.show', compact(
            'booking',
            'vehiclesAvailability',
            'pilotsAvailability'
        ));
    }

    public function approve(ApproveBookingRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();

        $updated = $this->availabilityService->approveBooking(
            $booking,
            $request->user(),
            isset($validated['vehicle_id']) ? (int) $validated['vehicle_id'] : null,
            isset($validated['pilot_id']) ? (int) $validated['pilot_id'] : null
        );

        $message = "Permohonan {$updated->booking_code} telah DILULUSKAN.";
        if ($updated->canDownloadMemo()) {
            $message .= ' Memo Kebenaran Memandu Kenderaan Pejabat kini sedia untuk dimuat turun.';
        }

        return redirect()->route('bookings.show', $updated)->with('success', $message);
    }

    public function reject(RejectBookingRequest $request, Booking $booking): RedirectResponse
    {
        if (! $booking->isPending()) {
            return back()->with('error', 'Hanya permohonan berstatus Menunggu Kelulusan (Pending) boleh ditolak.');
        }

        $validated = $request->validated();

        $booking->update([
            'status' => Booking::STATUS_REJECTED,
            'vehicle_id' => null,
            'pilot_id' => null,
            'rejection_reason' => $validated['rejection_reason'],
            'rejection_remarks' => $validated['rejection_remarks'] ?? null,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', "Permohonan {$booking->booking_code} telah direkodkan sebagai TIDAK DILULUSKAN.");
    }

    public function complete(CompleteTripRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();

        $completed = $this->availabilityService->completeTrip(
            $booking,
            isset($validated['odometer_start']) ? (int) $validated['odometer_start'] : null,
            isset($validated['odometer_end']) ? (int) $validated['odometer_end'] : null,
            $validated['pilot_remarks'] ?? null
        );

        $distance = $completed->totalDistance();
        $message = "Perjalanan {$completed->booking_code} telah direkodkan SELESAI.";
        if ($distance !== null) {
            $message .= " Jarak perjalanan: {$distance} km dan odometer kenderaan telah dikemaskini.";
        }

        if ($request->user()->isPilot()) {
            return redirect()->route('pilot.trips.index')->with('success', $message);
        }

        return redirect()->route('bookings.show', $completed)->with('success', $message);
    }

    public function destroy(Request $request, Booking $booking): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && $booking->user_id !== $user->id) {
            abort(403, 'Anda tidak dibenarkan membatalkan permohonan ini.');
        }

        if (! $booking->isPending() && ! ($user->isAdmin() && $booking->isRejected())) {
            return back()->with('error', 'Hanya permohonan berstatus Menunggu Kelulusan (Pending) boleh dibatalkan.');
        }

        $code = $booking->booking_code;
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', "Permohonan {$code} telah dibatalkan dan dipadam.");
    }

    private function authorizeView(User $user, Booking $booking): void
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

        abort(403, 'Anda tidak mempunyai kebenaran untuk melihat butiran tempahan ini.');
    }
}
