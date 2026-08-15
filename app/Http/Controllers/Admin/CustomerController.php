<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConventionBooking;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // These totals must use the same canonical room/payment calculation
        // as reports and invoices. SQL SUM(total_amount) misses discounts,
        // VAT, extra charges and later payments.
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments']);

        // Apply search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $allBookings = $query->get();
        $customerRows = $allBookings->groupBy('customer_phone')
            ->map(function ($bookings, $phone) {
                $latest = $bookings->sortByDesc('created_at')->first();

                return (object) [
                    'customer_phone' => $phone,
                    'customer_name' => $latest->customer_name,
                    'customer_email' => $latest->customer_email,
                    'customer_address' => $latest->customer_address,
                    'customer_nid' => $latest->customer_nid,
                    'company_name' => $latest->company_name,
                    'booking_count' => $bookings->count(),
                    'total_spent' => $bookings->sum(fn ($booking) => $booking->getGrandTotal()),
                    'total_paid' => $bookings->sum(fn ($booking) => $booking->getTotalDeposited()),
                    'total_due' => $bookings->sum(fn ($booking) => $booking->getCalculatedRemaining()),
                    'last_booking' => $latest->created_at,
                ];
            })
            ->sortByDesc('last_booking')
            ->values();

        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $customers = new LengthAwarePaginator(
            $customerRows->forPage($currentPage, $perPage)->values(),
            $customerRows->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats
        $totalCustomers = $allBookings->pluck('customer_phone')->filter()->unique()->count();
        $totalRevenue = $allBookings->sum(fn ($booking) => $booking->getGrandTotal());
        $totalDue = $allBookings->sum(fn ($booking) => $booking->getCalculatedRemaining());

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'totalRevenue', 'totalDue'));
    }

    public function conventionCustomers(Request $request)
    {
        // Get unique convention customer phones first with aggregates
        $query = ConventionBooking::select(
            'customer_phone',
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_email) as customer_email'),
            DB::raw('MAX(customer_address) as customer_address'),
            DB::raw('MAX(customer_nid) as customer_nid'),
            DB::raw('COUNT(*) as booking_count'),
            DB::raw('SUM(total_amount) as total_spent'),
            DB::raw('SUM(advance_payment) as total_paid'),
            DB::raw('SUM(remaining_payment) as total_due'),
            DB::raw('MAX(created_at) as last_booking')
        );

        // Apply search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $customers = $query->groupBy('customer_phone')
            ->orderByDesc('last_booking')
            ->paginate(20);

        // Stats
        $totalCustomers = ConventionBooking::distinct()->count('customer_phone');
        $totalRevenue = ConventionBooking::sum('total_amount');
        $totalDue = ConventionBooking::sum('remaining_payment');

        $isConvention = true;
        return view('admin.customers.index', compact('customers', 'totalCustomers', 'totalRevenue', 'totalDue', 'isConvention'));
    }

    public function show($phone)
    {
        // Decode URL-encoded phone
        $phone = urldecode($phone);
        
        // Get customer info from most recent booking
        $customer = Booking::where('customer_phone', $phone)->latest()->first() 
                ?? ConventionBooking::where('customer_phone', $phone)->latest()->first();

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer not found');
        }

        // Get all room bookings for this customer
        $roomBookings = Booking::where('customer_phone', $phone)
            ->with(['room.roomType', 'bookingRooms.room.roomType', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all convention bookings
        $conventionBookings = ConventionBooking::where('customer_phone', $phone)
            ->with(['conventionHall'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $stats = [
            'total_room_bookings' => $roomBookings->count(),
            'total_convention_bookings' => $conventionBookings->count(),
            'total_room_spent' => $roomBookings->sum(fn ($booking) => $booking->getGrandTotal()),
            'total_convention_spent' => $conventionBookings->sum('total_amount'),
            'total_room_paid' => $roomBookings->sum(fn ($booking) => $booking->getTotalDeposited()),
            'total_convention_paid' => $conventionBookings->sum('advance_payment'),
            'total_room_due' => $roomBookings->sum(fn ($booking) => $booking->getCalculatedRemaining()),
            'total_convention_due' => $conventionBookings->sum('remaining_payment'),
            'first_booking' => $roomBookings->merge($conventionBookings)->min('created_at'),
            'last_booking' => $roomBookings->merge($conventionBookings)->max('created_at'),
        ];

        return view('admin.customers.show', compact('customer', 'roomBookings', 'conventionBookings', 'stats'));
    }

    public function export(Request $request)
    {
        $customers = Booking::select(
            'customer_phone',
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_email) as customer_email'),
            DB::raw('MAX(customer_address) as customer_address'),
            DB::raw('MAX(company_name) as company_name')
        )
            ->groupBy('customer_phone')
            ->get();

        $csv = "Name,Phone,Email,Address,Company\n";
        foreach ($customers as $c) {
            $csv .= "\"{$c->customer_name}\",\"{$c->customer_phone}\",\"{$c->customer_email}\",\"{$c->customer_address}\",\"{$c->company_name}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename=customers-' . date('Y-m-d') . '.csv');
    }
}
