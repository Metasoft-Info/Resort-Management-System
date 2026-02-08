<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConventionBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Get unique customer phones first with aggregates
        $query = Booking::select(
            'customer_phone',
            DB::raw('MAX(customer_name) as customer_name'),
            DB::raw('MAX(customer_email) as customer_email'),
            DB::raw('MAX(customer_address) as customer_address'),
            DB::raw('MAX(customer_nid) as customer_nid'),
            DB::raw('MAX(company_name) as company_name'),
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
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->groupBy('customer_phone')
            ->orderByDesc('last_booking')
            ->paginate(20);

        // Stats
        $totalCustomers = Booking::distinct()->count('customer_phone');
        $totalRevenue = Booking::sum('total_amount');
        $totalDue = Booking::sum('remaining_payment');

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'totalRevenue', 'totalDue'));
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
            ->with(['room.roomType'])
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
            'total_room_spent' => $roomBookings->sum('total_amount'),
            'total_convention_spent' => $conventionBookings->sum('total_amount'),
            'total_room_paid' => $roomBookings->sum('advance_payment'),
            'total_convention_paid' => $conventionBookings->sum('advance_payment'),
            'total_room_due' => $roomBookings->sum('remaining_payment'),
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
