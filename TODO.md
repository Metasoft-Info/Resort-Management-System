# Booking Update 500 Fix - TODO

- [x] Review BookingController@update flow and identify risky calculations
- [x] Patch update flow for null-safe totals and non-negative remaining payment
- [x] Add defensive try/catch + logging for production-safe failure handling
- [x] Run PHP syntax check and summarize

# Combined Report Detail & Room Mapping Fix - TODO

- [x] Review combined report query + view rendering gaps (N/A room/room type)
- [x] Update combined/export queries to eager-load multi-room relations
- [x] Update combined tables to show room-bookings-like detailed columns
- [x] Add action button (view details) in each combined report table
- [x] Replace Bangla unpaid checked-in heading with clear English text
- [x] Run PHP syntax check and summarize

# Room Bookings Report - Total Deposited & Due Update - TODO

- [x] Review booking payment relationships/helpers for deposited calculation
- [x] Update roomBookings report query/summary to include total deposited
- [x] Update room-bookings blade: add "মোট জমা" in summary and table
- [x] Recalculate/display "বাকি" based on grand total - total deposited
- [x] Run syntax checks and summarize

# Dashboard Resort Room Availability Sync Fix (Production) - TODO

- [x] Review dashboard occupied-room calculation (legacy room_id vs booking_rooms)
- [x] Update DashboardController to merge occupied IDs from both sources
- [x] Apply merged occupied IDs to stats and room status cards
- [x] Run PHP syntax check and summarize
