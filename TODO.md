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
