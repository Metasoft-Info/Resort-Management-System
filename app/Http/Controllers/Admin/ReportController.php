<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking, RoomType, Room, ConventionHall, ResortInfo, ExtraChargeCategory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller {
    public function roomBookings(Request $request) {
        $today = date('Y-m-d');
        $dueOnly = $request->boolean('due_only');
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments', 'financialSnapshots']);

        // Due Only filter: show only checked-out bookings with remaining payment > 0
        if ($dueOnly) {
            $query->where('status', 'checked_out');
            // We'll filter by remaining > 0 after fetching since it's calculated
        } elseif ($request->start_date || $request->end_date) {
            $start = $request->start_date ?: $today;
            $end = $request->end_date ?: $start;
            $isSingleDate = ($start === $end);

            $query->where(function ($q) use ($start, $end, $isSingleDate) {
                if ($isSingleDate) {
                    // Single date: only show activity on this specific date
                    $q->whereDate('check_in_date', $start)
                      ->orWhereDate('check_out_date', $start)
                      ->orWhere(function ($qq) use ($start) {
                          $qq->whereDate('check_in_date', '<=', $start)
                             ->whereDate('check_out_date', '>', $start)
                             ->where('status', '!=', 'cancelled');
                      })
                      ->orWhere(function ($qq) use ($start) {
                          $qq->where('status', 'cancelled')
                             ->whereDate('check_in_date', $start);
                      })
                      ->orWhereHas('payments', function ($pq) use ($start) {
                          $pq->whereDate('payment_date', $start)
                             ->whereIn('type', ['advance', 'payment', 'refund']);
                      })
                      ->orWhereHas('financialSnapshots', function ($sq) use ($start) {
                          $sq->whereDate('effective_date', $start);
                      });
                } else {
                    $q->whereBetween(\DB::raw('DATE(check_in_date)'), [$start, $end])
                      ->orWhereBetween(\DB::raw('DATE(check_out_date)'), [$start, $end])
                      ->orWhere(function ($qq) use ($start, $end) {
                          $qq->whereDate('check_in_date', '<=', $end)
                             ->whereDate('check_out_date', '>=', $start)
                             ->where('status', '!=', 'cancelled');
                      })
                      ->orWhere(function ($qq) use ($start, $end) {
                          $qq->where('status', 'cancelled')
                             ->whereDate('check_in_date', '<=', $end)
                             ->whereDate('check_in_date', '>=', $start);
                      })
                      ->orWhereHas('payments', function ($pq) use ($start, $end) {
                          $pq->whereDate('payment_date', '>=', $start)
                             ->whereDate('payment_date', '<=', $end)
                             ->whereIn('type', ['advance', 'payment', 'refund']);
                      })
                      ->orWhereHas('financialSnapshots', function ($sq) use ($start, $end) {
                          $sq->whereDate('effective_date', '>=', $start)
                             ->whereDate('effective_date', '<=', $end);
                      });
                }
            });
        } else {
            $query->where(function ($q) use ($today) {
                $q->where('status', 'checked_in')
                  ->orWhere(function ($qq) use ($today) {
                      $qq->where('status', 'checked_out')
                         ->whereDate('check_out_date', $today);
                  })
                  ->orWhere(function ($qq) use ($today) {
                      $qq->where('status', 'cancelled')
                         ->whereDate('check_in_date', '>=', $today);
                  })
                  ->orWhereHas('financialSnapshots', function ($sq) use ($today) {
                      $sq->whereDate('effective_date', $today);
                  });
            });
        }

        if($request->status) $query->where('status', $request->status);
        if($request->room_type_id) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('room', fn($room) => $room->where('room_type_id', $request->room_type_id))
                  ->orWhereHas('bookingRooms.room', fn($room) => $room->where('room_type_id', $request->room_type_id));
            });
        }
        if($request->room_id) {
            $query->where(function ($q) use ($request) {
                $q->where('room_id', $request->room_id)
                  ->orWhereHas('bookingRooms', fn($bookingRoom) => $bookingRoom->where('room_id', $request->room_id));
            });
        }
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->discount_status) {
            if($request->discount_status === 'has_discount') {
                $query->where(function($q) {
                    $q->where('discount_amount', '>', 0)
                      ->orWhere(function($sq) {
                          $sq->where('discount_type', 'percentage')->where('discount_percentage', '>', 0);
                      });
                });
            } else {
                $query->where('discount_status', $request->discount_status);
            }
        }
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('customer_nid', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"));
            });
        }
        
        $summaryBookings = (clone $query)->get();
        
        // Filter by remaining > 0 for due_only mode
        if ($dueOnly) {
            $summaryBookings = $summaryBookings->filter(fn($b) => $b->getCalculatedRemaining() > 0);
        }
        
        // Determine the filter date for counts
        $filterEndDate = $request->end_date ?: ($request->start_date ?: date('Y-m-d'));
        $filterStartDate = $request->start_date ?: ($request->end_date ?: date('Y-m-d'));

        $reportFinancials = $summaryBookings->mapWithKeys(function ($booking) use ($filterStartDate, $filterEndDate, $dueOnly) {
            return [
                $booking->id => $booking->getReportFinancials(
                    $filterStartDate,
                    $filterEndDate,
                    $dueOnly
                ),
            ];
        });

        $sumFinancial = fn (string $key) => $reportFinancials->sum(fn ($financials) => (float) ($financials[$key] ?? 0));
        $summaryRoomRent = $sumFinancial('room_rent');
        $summaryDiscount = $sumFinancial('discount');
        $summaryExtra = $sumFinancial('extra_charges');
        $totalBookings = $summaryBookings->count();
        $totalRevenue = $sumFinancial('grand_total');
        $totalAdvance = $sumFinancial('advance');
        $totalDeposited = $sumFinancial('deposited');
        $totalRemaining = $sumFinancial('remaining');

        // Booking count breakdown - use filtered date, not actual today
        $confirmedCount = $summaryBookings->where('status', 'confirmed')->count();
        $cancelledCount = $summaryBookings->where('status', 'cancelled')->count();

        $isSingleDate = ($filterStartDate === $filterEndDate);

        if ($isSingleDate) {
            // Old Guest: checked in before the filter date and still staying (count by bookings, not unique guests)
            $oldGuestCount = $summaryBookings->filter(fn($b) =>
                $b->status === 'checked_in' &&
                $b->check_in_date &&
                $b->check_in_date->format('Y-m-d') < $filterEndDate
            )->count();

            // In Guest: checked in on the filter date
            $inGuestCount = $summaryBookings->filter(fn($b) =>
                $b->check_in_date &&
                $b->check_in_date->format('Y-m-d') == $filterEndDate
            )->count();

            // Checkout: checked out on the filter date (regardless of payment status)
            $checkoutCount = $summaryBookings->filter(fn($b) =>
                $b->status === 'checked_out' &&
                $b->check_out_date &&
                $b->check_out_date->format('Y-m-d') == $filterEndDate
            )->count();

            // Due Clear: checked out before the filter date but made a payment on the filter date (came back to clear due)
            $dueClearCount = $summaryBookings->filter(fn($b) =>
                $b->status === 'checked_out' &&
                $b->check_out_date &&
                $b->check_out_date->format('Y-m-d') < $filterEndDate &&
                $b->payments->contains(fn($p) =>
                    $p->type !== 'refund' &&
                    ($p->payment_date?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d')) == $filterEndDate
                )
            )->count();
        } else {
            // Date range mode
            // Old Guest: checked in before the range start and still staying
            $oldGuestCount = $summaryBookings->filter(fn($b) =>
                $b->status === 'checked_in' &&
                $b->check_in_date &&
                $b->check_in_date->format('Y-m-d') < $filterStartDate
            )->count();

            // In Guest: checked in within the date range
            $inGuestCount = $summaryBookings->filter(fn($b) =>
                $b->check_in_date &&
                $b->check_in_date->format('Y-m-d') >= $filterStartDate &&
                $b->check_in_date->format('Y-m-d') <= $filterEndDate
            )->count();

            // Checkout: checked out within the date range (regardless of payment status)
            $checkoutCount = $summaryBookings->filter(fn($b) =>
                $b->status === 'checked_out' &&
                $b->check_out_date &&
                $b->check_out_date->format('Y-m-d') >= $filterStartDate &&
                $b->check_out_date->format('Y-m-d') <= $filterEndDate
            )->count();

            // Due Clear: checked out before the range but made a payment within the date range (came back to clear due)
            $dueClearCount = $summaryBookings->filter(fn($b) =>
                $b->status === 'checked_out' &&
                $b->check_out_date &&
                $b->check_out_date->format('Y-m-d') < $filterStartDate &&
                $b->payments->contains(fn($p) =>
                    $p->type !== 'refund' &&
                    ($p->payment_date?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d')) >= $filterStartDate &&
                    ($p->payment_date?->format('Y-m-d') ?? $p->created_at?->format('Y-m-d')) <= $filterEndDate
                )
            )->count();
        }
        
        if ($dueOnly) {
            // Manual pagination for due_only since we filter in collection
            $dueBookings = $summaryBookings->sortByDesc('check_in_date')->values();
            $perPage = 20;
            $currentPage = request()->get('page', 1);
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
                $dueBookings->forPage($currentPage, $perPage),
                $dueBookings->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $bookings = $query->orderBy('check_in_date', 'desc')->paginate(20)->withQueryString();
        }
        $roomTypes = RoomType::all();
        $rooms = Room::orderBy('room_number')->get();
        $resortInfo = ResortInfo::first();
        
        return view('admin.reports.room-bookings', compact(
            'bookings',
            'totalRevenue',
            'totalBookings',
            'totalAdvance',
            'totalDeposited',
            'totalRemaining',
            'summaryRoomRent',
            'summaryDiscount',
            'summaryExtra',
            'reportFinancials',
            'roomTypes',
            'rooms',
            'resortInfo',
            'filterEndDate',
            'filterStartDate',
            'oldGuestCount',
            'inGuestCount',
            'checkoutCount',
            'dueClearCount',
            'confirmedCount',
            'cancelledCount'
        ));
    }
    
    public function exportRoomBookings(Request $request) {
        $today = date('Y-m-d');
        $dueOnly = $request->boolean('due_only');
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments', 'financialSnapshots']);

        if ($dueOnly) {
            $query->where('status', 'checked_out');
        } elseif ($request->start_date || $request->end_date) {
            $start = $request->start_date ?: $today;
            $end = $request->end_date ?: $start;

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween(\DB::raw('DATE(check_in_date)'), [$start, $end])
                  ->orWhereBetween(\DB::raw('DATE(check_out_date)'), [$start, $end])
                  ->orWhere(function ($qq) use ($start, $end) {
                      $qq->whereDate('check_in_date', '<=', $end)
                         ->whereDate('check_out_date', '>=', $start)
                         ->where('status', '!=', 'cancelled');
                  })
                  ->orWhereHas('payments', function ($pq) use ($start, $end) {
                      $pq->whereDate('payment_date', '>=', $start)
                         ->whereDate('payment_date', '<=', $end)
                         ->whereIn('type', ['advance', 'payment', 'refund']);
                  })
                  ->orWhereHas('financialSnapshots', function ($sq) use ($start, $end) {
                      $sq->whereDate('effective_date', '>=', $start)
                         ->whereDate('effective_date', '<=', $end);
                  });
            });
        } else {
            $query->where(function ($q) use ($today) {
                $q->where('status', 'checked_in')
                  ->orWhere(function ($qq) use ($today) {
                      $qq->where('status', 'checked_out')
                         ->whereDate('check_out_date', $today);
                  })
                  ->orWhereHas('financialSnapshots', function ($sq) use ($today) {
                      $sq->whereDate('effective_date', $today);
                  });
            });
        }

        if($request->status) $query->where('status', $request->status);
        if($request->room_type_id) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('room', fn($room) => $room->where('room_type_id', $request->room_type_id))
                  ->orWhereHas('bookingRooms.room', fn($room) => $room->where('room_type_id', $request->room_type_id));
            });
        }
        if($request->room_id) {
            $query->where(function ($q) use ($request) {
                $q->where('room_id', $request->room_id)
                  ->orWhereHas('bookingRooms', fn($bookingRoom) => $bookingRoom->where('room_id', $request->room_id));
            });
        }
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->discount_status) {
            if($request->discount_status === 'has_discount') {
                $query->where(function($q) {
                    $q->where('discount_amount', '>', 0)
                      ->orWhere(function($sq) {
                          $sq->where('discount_type', 'percentage')->where('discount_percentage', '>', 0);
                      });
                });
            } else {
                $query->where('discount_status', $request->discount_status);
            }
        }
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('customer_nid', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"))
                  ->orWhereHas('bookingRooms.room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"));
            });
        }
        
        $bookings = $query->orderBy('check_in_date', 'desc')->get();
        if ($dueOnly) {
            $bookings = $bookings->filter(fn($booking) => $booking->getCalculatedRemaining() > 0)->values();
        }
        $filterEndDate = $request->end_date ?: ($request->start_date ?: date('Y-m-d'));
        $filterStartDate = $request->start_date ?: ($request->end_date ?: date('Y-m-d'));
        
        $csvContent = "ID,Customer Name,Phone,NID,Room,Room Type,Check-In,Check-Out,Room Rent,Discount,Extra Charges,Grand Total,Advance,Deposited,Remaining,Payment Status,Status (As of {$filterEndDate})\n";
        foreach ($bookings as $b) {
            $financials = $b->getReportFinancials($filterStartDate, $filterEndDate, $dueOnly);
            $reportCheckInDate = $financials['check_in_date'] ?? $b->check_in_date;
            $reportCheckOutDate = $financials['check_out_date'] ?? $b->check_out_date;
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"{$b->customer_nid}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"" . ($b->room->roomType->name ?? 'N/A') . "\",";
            $csvContent .= "\"{$reportCheckInDate}\",";
            $csvContent .= "\"{$reportCheckOutDate}\",";
            $csvContent .= "\"{$financials['room_rent']}\",";
            $csvContent .= "\"{$financials['discount']}\",";
            $csvContent .= "\"{$financials['extra_charges']}\",";
            $csvContent .= "\"{$financials['grand_total']}\",";
            $csvContent .= "\"{$financials['advance']}\",";
            $csvContent .= "\"{$financials['deposited']}\",";
            $csvContent .= "\"{$financials['remaining']}\",";
            $csvContent .= "\"{$b->payment_status}\",";
            $csvContent .= "\"" . $b->getStatusAsOfDate($filterEndDate) . "\"\n";
        }
        
        $filename = 'room-bookings-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
    
    public function advanceBookings(Request $request) {
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments'])
            ->where('check_in_date', '>', date('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
        
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->room_type_id) $query->whereHas('room', fn($q) => $q->where('room_type_id', $request->room_type_id));
        if($request->room_id) $query->where('room_id', $request->room_id);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%");
            });
        }
        
        $totalAdvance = (clone $query)->sum('advance_payment');
        $totalBookings = (clone $query)->count();
        $totalRevenue = (clone $query)->with('bookingRooms')->get()->sum(fn ($booking) => $booking->getGrandTotal());
        
        $bookings = $query->orderBy('check_in_date', 'asc')->paginate(20)->withQueryString();
        $roomTypes = RoomType::all();
        $resortInfo = ResortInfo::first();
        
        return view('admin.reports.advance-bookings', compact('bookings', 'totalAdvance', 'totalBookings', 'totalRevenue', 'roomTypes', 'resortInfo'));
    }
    
    public function exportAdvanceBookings(Request $request) {
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments'])
            ->where('check_in_date', '>', date('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
        
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%");
            });
        }
        
        $bookings = $query->orderBy('check_in_date', 'asc')->get();
        
        $csvContent = "ID,Customer Name,Phone,Room,Check-In,Check-Out,Total,Bill Night,Rent,Advance,Status\n";
        foreach ($bookings as $b) {
            $nights = $b->getNights();
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->check_in_date}\",";
            $csvContent .= "\"{$b->check_out_date}\",";
            $csvContent .= "\"{$nights}\",";
            $csvContent .= "\"{$b->getGrandTotal()}\",";
            $csvContent .= "\"{$b->getCalculatedTotal()}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $filename = 'advance-bookings-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
    
    public function unpaidCheckedIn(Request $request) {
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments'])
            ->where('status', 'checked_in')
            ->where(function($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhereRaw('remaining_payment > 0');
            });
        
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->room_type_id) $query->whereHas('room', fn($q) => $q->where('room_type_id', $request->room_type_id));
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"));
            });
        }
        
        $summaryBookings = (clone $query)->get();
        $totalUnpaid = $summaryBookings->sum(fn($b) => $b->getCalculatedRemaining());
        $totalAdvance = $summaryBookings->sum('advance_payment');
        $totalBookings = $summaryBookings->count();
        
        $bookings = $query->orderBy('check_in_date', 'desc')->paginate(20)->withQueryString();
        $roomTypes = RoomType::all();
        $resortInfo = ResortInfo::first();
        
        return view('admin.reports.unpaid-checked-in', compact('bookings', 'totalUnpaid', 'totalAdvance', 'totalBookings', 'roomTypes', 'resortInfo'));
    }
    
    public function exportUnpaidCheckedIn(Request $request) {
        $query = Booking::with(['room.roomType', 'bookingRooms.room.roomType', 'payments'])
            ->where('status', 'checked_in')
            ->where(function($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhereRaw('remaining_payment > 0');
            });
        
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"));
            });
        }
        
        $bookings = $query->orderBy('check_in_date', 'desc')->get();
        
        $csvContent = "ID,Customer Name,Phone,Room,Check-In,Total,Advance,Bill,Due,Status\n";
        foreach ($bookings as $b) {
            $due = $b->getCalculatedRemaining();
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->check_in_date}\",";
            $csvContent .= "\"{$b->getGrandTotal()}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->getCalculatedTotal()}\",";
            $csvContent .= "\"{$due}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $filename = 'unpaid-checked-in-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
    
    public function combined(Request $request) {
        $roomTypeFilter = $request->room_type_id;
        
        $roomBookingsQuery = Booking::with(['room.roomType', 'bookingRooms.room.roomType'])
            ->orderBy('check_in_date', 'desc');
        $advanceBookingsQuery = Booking::with(['room.roomType', 'bookingRooms.room.roomType'])
            ->where('check_in_date', '>', date('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->orderBy('check_in_date', 'asc');
        $unpaidBookingsQuery = Booking::with(['room.roomType', 'bookingRooms.room.roomType'])
            ->where('status', 'checked_in')
            ->where(function($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhereRaw('remaining_payment > 0');
            })
            ->orderBy('check_in_date', 'desc');
        
        if($roomTypeFilter) {
            $roomBookingsQuery->whereHas('room', fn($q) => $q->where('room_type_id', $roomTypeFilter));
            $advanceBookingsQuery->whereHas('room', fn($q) => $q->where('room_type_id', $roomTypeFilter));
            $unpaidBookingsQuery->whereHas('room', fn($q) => $q->where('room_type_id', $roomTypeFilter));
        }
        
        if($request->start_date) {
            $roomBookingsQuery->whereDate('check_in_date', '>=', $request->start_date);
            $unpaidBookingsQuery->whereDate('check_in_date', '>=', $request->start_date);
        }
        if($request->end_date) {
            $roomBookingsQuery->whereDate('check_in_date', '<=', $request->end_date);
            $unpaidBookingsQuery->whereDate('check_in_date', '<=', $request->end_date);
        }
        
        $allRoomBookings = $roomBookingsQuery->get();
        $allAdvanceBookings = $advanceBookingsQuery->get();
        $allUnpaidBookings = $unpaidBookingsQuery->get();
        
        $roomBookings = $allRoomBookings->take(20);
        $advanceBookings = $allAdvanceBookings->take(20);
        $unpaidBookings = $allUnpaidBookings->take(20);
        
        $roomBookingsCount = $allRoomBookings->count();
        $advanceCount = $allAdvanceBookings->count();
        $unpaidCount = $allUnpaidBookings->count();
        
        $grandTotalBookings = $roomBookingsCount;
        $grandTotalRevenue = $allRoomBookings->sum(fn($b) => $b->getGrandTotal());
        $grandTotalRemaining = $allRoomBookings->sum(fn($b) => $b->getCalculatedRemaining());
        
        $roomTypes = RoomType::all();
        $resortInfo = ResortInfo::first();
        
        return view('admin.reports.combined', compact(
            'roomBookings', 'advanceBookings', 'unpaidBookings',
            'roomBookingsCount', 'advanceCount', 'unpaidCount',
            'grandTotalBookings', 'grandTotalRevenue', 'grandTotalRemaining',
            'roomTypes', 'resortInfo'
        ));
    }
    
    public function exportCombined(Request $request) {
        $roomTypeFilter = $request->room_type_id;
        
        $roomBookingsQuery = Booking::with(['room.roomType', 'bookingRooms.room.roomType']);
        $advanceBookingsQuery = Booking::with(['room.roomType', 'bookingRooms.room.roomType'])
            ->where('check_in_date', '>', date('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
        $unpaidBookingsQuery = Booking::with(['room.roomType', 'bookingRooms.room.roomType'])
            ->where('status', 'checked_in')
            ->where(function($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhereRaw('remaining_payment > 0');
            });
        
        if($roomTypeFilter) {
            $roomBookingsQuery->whereHas('room', fn($q) => $q->where('room_type_id', $roomTypeFilter));
            $advanceBookingsQuery->whereHas('room', fn($q) => $q->where('room_type_id', $roomTypeFilter));
            $unpaidBookingsQuery->whereHas('room', fn($q) => $q->where('room_type_id', $roomTypeFilter));
        }
        
        if($request->start_date) {
            $roomBookingsQuery->whereDate('check_in_date', '>=', $request->start_date);
            $unpaidBookingsQuery->whereDate('check_in_date', '>=', $request->start_date);
        }
        if($request->end_date) {
            $roomBookingsQuery->whereDate('check_in_date', '<=', $request->end_date);
            $unpaidBookingsQuery->whereDate('check_in_date', '<=', $request->end_date);
        }
        
        $allRoomBookings = $roomBookingsQuery->get();
        $allAdvanceBookings = $advanceBookingsQuery->get();
        $allUnpaidBookings = $unpaidBookingsQuery->get();
        
        $csvContent = "=== ROOM BOOKINGS ===\n";
        $csvContent .= "ID,Customer Name,Phone,Room,Check-In,Check-Out,Total,Advance,Remaining,Status\n";
        foreach ($allRoomBookings as $b) {
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->check_in_date}\",";
            $csvContent .= "\"{$b->check_out_date}\",";
            $csvContent .= "\"{$b->getGrandTotal()}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->remaining_payment}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $csvContent .= "\n=== ADVANCE BOOKINGS ===\n";
        $csvContent .= "ID,Customer Name,Phone,Room,Check-In,Check-Out,Nights,Total,Advance,Status\n";
        foreach ($allAdvanceBookings as $b) {
            $nights = $b->getNights();
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->check_in_date}\",";
            $csvContent .= "\"{$b->check_out_date}\",";
            $csvContent .= "\"{$nights}\",";
            $csvContent .= "\"{$b->getGrandTotal()}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $csvContent .= "\n=== UNPAID CHECKED-IN ===\n";
        $csvContent .= "ID,Customer Name,Phone,Room,Check-In,Total,Advance,Bill,Due,Status\n";
        foreach ($allUnpaidBookings as $b) {
            $due = $b->getCalculatedRemaining();
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->check_in_date}\",";
            $csvContent .= "\"{$b->getGrandTotal()}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->getCalculatedTotal()}\",";
            $csvContent .= "\"{$due}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $filename = 'combined-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
    
    public function conventionBookings(Request $request) {
        $query = ConventionBooking::with('conventionHall');
        if($request->start_date) $query->whereDate('event_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('event_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->hall_id) $query->where('convention_hall_id', $request->hall_id);
        if($request->time_slot) $query->where('time_slot', $request->time_slot);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('organization_name', 'like', "%{$request->search}%");
            });
        }

        $allBookings = $query->orderBy('event_date', 'desc')->orderBy('id')->get();

        // Group by customer + event date + time slot for multi-hall display
        $grouped = $allBookings->groupBy(function($b) {
            return $b->customer_phone . '|' . $b->event_date . '|' . $b->time_slot;
        })->map(function($group) {
            $first = $group->first();
            $paymentStatusPriority = ['unpaid' => 3, 'partial' => 2, 'paid' => 1];
            $groupPaymentStatus = $group->sortByDesc(function($b) use ($paymentStatusPriority) {
                return $paymentStatusPriority[$b->payment_status] ?? 0;
            })->first()->payment_status;

            return (object)[
                'id' => $first->id,
                'ids' => $group->pluck('id')->toArray(),
                'event_date' => $first->event_date,
                'customer_phone' => $first->customer_phone,
                'customer_name' => $first->customer_name,
                'organization_name' => $first->organization_name,
                'time_slot' => $first->time_slot,
                'halls' => $group->pluck('conventionHall.name')->filter()->values(),
                'hall_count' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
                'advance_payment' => $group->sum('advance_payment'),
                'vat_amount' => $group->sum('vat_amount'),
                'remaining_payment' => $group->sum('remaining_payment'),
                'payment_status' => $groupPaymentStatus,
                'notes' => $first->notes,
            ];
        })->values();

        $totalBookings = $grouped->count();
        $totalRevenue = $grouped->sum('total_amount');
        $totalAdvance = $grouped->sum('advance_payment');
        $totalRemaining = $grouped->sum('remaining_payment');
        $totalVat = $grouped->sum('vat_amount');

        // Paginate grouped collection
        $page = $request->get('page', 1);
        $perPage = 20;
        $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($page, $perPage),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $bookings = $paginatedGroups;
        $halls = ConventionHall::all();
        $resortInfo = ResortInfo::first();

        return view('admin.reports.convention-bookings', compact('bookings', 'totalRevenue', 'totalBookings', 'totalAdvance', 'totalRemaining', 'totalVat', 'halls', 'resortInfo'));
    }

    public function policeStation(Request $request) {
        $today = date('Y-m-d');
        $query = Booking::with(['room.roomType', 'bookingRooms.room', 'additionalGuests', 'createdBy']);

        if ($request->start_date || $request->end_date) {
            $start = $request->start_date ?: $today;
            $end = $request->end_date ?: $start;
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween(\DB::raw('DATE(check_in_date)'), [$start, $end])
                  ->orWhereBetween(\DB::raw('DATE(check_out_date)'), [$start, $end])
                  ->orWhere(function ($qq) use ($start, $end) {
                      $qq->whereDate('check_in_date', '<=', $end)
                         ->whereDate('check_out_date', '>=', $start)
                         ->where('status', 'checked_in');
                  });
            });
        } else {
            $query->where(function ($q) use ($today) {
                $q->where('status', 'checked_in')
                  ->orWhere(function ($qq) use ($today) {
                      $qq->where('status', 'checked_out')
                         ->whereDate('check_out_date', $today);
                  });
            });
        }

        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('customer_nid', 'like', "%{$request->search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"));
            });
        }

        $bookings = $query->orderBy('check_in_date', 'desc')->paginate(20)->withQueryString();
        $roomTypes = RoomType::all();
        $rooms = Room::orderBy('room_number')->get();
        $resortInfo = ResortInfo::first();

        return view('admin.reports.police-station', compact('bookings', 'roomTypes', 'rooms', 'resortInfo'));
    }

    public function guestExtraCharges(Request $request) {
        $today = date('Y-m-d');
        $query = Booking::with(['room.roomType', 'bookingRooms.room', 'createdBy'])
            ->where(function($q) {
                $q->where('extra_charges', '>', 0)
                  ->orWhereNotNull('extra_charges_data');
            });

        if ($request->start_date || $request->end_date) {
            $start = $request->start_date ?: $today;
            $end = $request->end_date ?: $start;
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween(\DB::raw('DATE(check_in_date)'), [$start, $end])
                  ->orWhereBetween(\DB::raw('DATE(check_out_date)'), [$start, $end])
                  ->orWhere(function ($qq) use ($start, $end) {
                      $qq->whereDate('check_in_date', '<=', $end)
                         ->whereDate('check_out_date', '>=', $start);
                  });
            });
        }

        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$request->search}%"));
            });
        }

        $bookings = $query->orderBy('check_in_date', 'desc')->paginate(20)->withQueryString();
        $categories = ExtraChargeCategory::active()->orderBy('order')->get();
        $rooms = Room::orderBy('room_number')->get();
        $resortInfo = ResortInfo::first();

        return view('admin.reports.guest-extra-charges', compact('bookings', 'categories', 'rooms', 'resortInfo'));
    }
    
    public function exportConventionBookings(Request $request) {
        $query = ConventionBooking::with('conventionHall');
        if($request->start_date) $query->whereDate('event_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('event_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->hall_id) $query->where('convention_hall_id', $request->hall_id);
        if($request->time_slot) $query->where('time_slot', $request->time_slot);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        
        $bookings = $query->orderBy('event_date', 'desc')->get();
        
        $csvContent = "ID,Customer Name,Organization,Phone,Hall,Event Date,Time Slot,Total Amount,Advance,Remaining,Payment Status,Status\n";
        foreach ($bookings as $b) {
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->organization_name ?? '') . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->conventionHall->name ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->event_date}\",";
            $csvContent .= "\"{$b->time_slot}\",";
            $csvContent .= "\"{$b->total_amount}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->remaining_payment}\",";
            $csvContent .= "\"{$b->payment_status}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $filename = 'convention-bookings-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
