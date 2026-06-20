<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConventionBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DiscountApprovalController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->type ?? 'all';

        // Date filters
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : null;
        $statusFilter = $request->status;

        // Defensive: if discount columns don't exist yet, return empty safe view
        $hasRoomDiscountCols = Schema::hasColumn('bookings', 'discount_status')
            && Schema::hasColumn('bookings', 'discount_requested_by')
            && Schema::hasColumn('bookings', 'discount_approved_by');
        $hasConvDiscountCols = Schema::hasColumn('convention_bookings', 'discount_status')
            && Schema::hasColumn('convention_bookings', 'discount_requested_by')
            && Schema::hasColumn('convention_bookings', 'discount_approved_by');

        if (!$hasRoomDiscountCols && !$hasConvDiscountCols) {
            return view('admin.discount-approval.index', [
                'allBookings' => collect([]),
                'pendingCount' => 0,
                'approvedCount' => 0,
                'rejectedCount' => 0,
                'pendingAmount' => 0,
                'approvedAmount' => 0,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'statusFilter' => $statusFilter,
                'typeFilter' => $typeFilter,
                'needsMigration' => true,
            ]);
        }

        $allBookings = collect([]);
        $roomBookings = collect([]);
        $conventionBookings = collect([]);

        // Room bookings with discounts
        if ($hasRoomDiscountCols && ($typeFilter === 'all' || $typeFilter === 'room')) {
            $roomQuery = Booking::with(['createdBy', 'discountRequestedBy', 'discountApprovedBy', 'room', 'bookingRooms.room', 'payments'])
                ->where(function($q) {
                    $q->whereNotNull('discount_status')
                      ->orWhere(function($sq) {
                          $sq->where(function($sqq) {
                              $sqq->where('discount_amount', '>', 0)
                                  ->orWhere(function($ssqq) {
                                      $ssqq->where('discount_type', 'percentage')->where('discount_percentage', '>', 0);
                                  });
                          })->whereNull('discount_status');
                      });
                });

            if ($dateFrom) $roomQuery->whereDate('created_at', '>=', $dateFrom);
            if ($dateTo) $roomQuery->whereDate('created_at', '<=', $dateTo);
            if ($statusFilter) $roomQuery->where('discount_status', $statusFilter);
            $roomBookings = $roomQuery->latest()->get();
        }

        // Convention bookings with discounts
        if ($hasConvDiscountCols && ($typeFilter === 'all' || $typeFilter === 'convention')) {
            $conventionQuery = ConventionBooking::with(['createdBy', 'discountRequestedBy', 'discountApprovedBy', 'conventionHall', 'payments'])
                ->where(function($q) {
                    $q->whereNotNull('discount_status')
                      ->orWhere(function($sq) {
                          $sq->where('discount', '>', 0)->whereNull('discount_status');
                      });
                });

            if ($dateFrom) $conventionQuery->whereDate('created_at', '>=', $dateFrom);
            if ($dateTo) $conventionQuery->whereDate('created_at', '<=', $dateTo);
            if ($statusFilter) $conventionQuery->where('discount_status', $statusFilter);
            $conventionBookings = $conventionQuery->latest()->get();
        }

        // Merge and sort by created_at desc
        $allBookings = $roomBookings->concat($conventionBookings)->sortByDesc('created_at');

        // Stats (always all types)
        $allRoom = $hasRoomDiscountCols ? Booking::where(function($q) {
            $q->whereNotNull('discount_status')
              ->orWhere(function($sq) {
                  $sq->where(function($sqq) {
                      $sqq->where('discount_amount', '>', 0)
                          ->orWhere(function($ssqq) {
                              $ssqq->where('discount_type', 'percentage')->where('discount_percentage', '>', 0);
                          });
                  })->whereNull('discount_status');
              });
        })->get() : collect([]);

        $allConv = $hasConvDiscountCols ? ConventionBooking::where(function($q) {
            $q->whereNotNull('discount_status')
              ->orWhere(function($sq) { $sq->where('discount', '>', 0)->whereNull('discount_status'); });
        })->get() : collect([]);

        $allForStats = $allRoom->concat($allConv);

        $pendingCount = $allForStats->where('discount_status', 'pending')->count();
        $approvedCount = $allForStats->where('discount_status', 'approved')->count();
        $rejectedCount = $allForStats->where('discount_status', 'rejected')->count();
        $pendingAmount = $allForStats->where('discount_status', 'pending')->sum(function ($b) {
            return $this->getDiscountAmount($b);
        });
        $approvedAmount = $allForStats->where('discount_status', 'approved')->sum(function ($b) {
            return $this->getDiscountAmount($b);
        });

        return view('admin.discount-approval.index', compact(
            'allBookings',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingAmount',
            'approvedAmount',
            'dateFrom',
            'dateTo',
            'statusFilter',
            'typeFilter'
        ));
    }

    public function show(Request $request, $type, $id)
    {
        if ($type === 'room') {
            $booking = Booking::with(['room', 'bookingRooms.room', 'payments.recordedBy', 'additionalGuests', 'foodPackage', 'createdBy', 'discountRequestedBy', 'discountApprovedBy'])->findOrFail($id);
        } else {
            $booking = ConventionBooking::with(['conventionHall', 'payments', 'foodPackage', 'createdBy', 'discountRequestedBy', 'discountApprovedBy'])->findOrFail($id);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'booking' => $booking,
                'type' => $type,
                'discountAmount' => $this->getDiscountAmount($booking),
                'discountRequestedByName' => $booking->discountRequestedBy?->name ?? 'System',
                'discountApprovedByName' => $booking->discountApprovedBy?->name ?? '-',
            ]);
        }

        return redirect()->route('admin.discount-approval.index');
    }

    public function approve(Request $request, $type, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveDiscounts()) {
            return redirect()->back()->with('error', 'You do not have permission to approve discounts.');
        }

        $table = $type === 'room' ? 'bookings' : 'convention_bookings';
        if (!Schema::hasColumn($table, 'discount_status')) {
            return redirect()->back()->with('error', 'Database migration required. Please run php artisan migrate.');
        }

        if ($type === 'room') {
            $booking = Booking::findOrFail($id);
        } else {
            $booking = ConventionBooking::findOrFail($id);
        }

        // Recalculate payment status after discount approval
        $remaining = max(0, $booking->getCalculatedRemaining());
        $booking->update([
            'discount_status' => 'approved',
            'discount_approved_by' => $user->id,
            'discount_approved_at' => now(),
            'remaining_payment' => $remaining,
            'payment_status' => $remaining <= 0 ? 'paid' : ($booking->getTotalDeposited() > 0 ? 'partial' : 'pending'),
        ]);

        return redirect()->back()->with('success', 'Discount approved successfully.');
    }

    public function reject(Request $request, $type, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveDiscounts()) {
            return redirect()->back()->with('error', 'You do not have permission to reject discounts.');
        }

        $table = $type === 'room' ? 'bookings' : 'convention_bookings';
        if (!Schema::hasColumn($table, 'discount_status')) {
            return redirect()->back()->with('error', 'Database migration required. Please run php artisan migrate.');
        }

        if ($type === 'room') {
            $booking = Booking::findOrFail($id);
        } else {
            $booking = ConventionBooking::findOrFail($id);
        }

        $booking->update([
            'discount_status' => 'rejected',
            'discount_approved_by' => $user->id,
            'discount_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Discount rejected successfully.');
    }

    private function getDiscountAmount($booking)
    {
        if ($booking instanceof Booking) {
            if ($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
                $baseAmount = $booking->getCalculatedTotal();
                return ($baseAmount * $booking->discount_percentage) / 100;
            }
            return $booking->discount_amount ?? 0;
        }
        // ConventionBooking
        return $booking->discount ?? 0;
    }
}
