<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Models\ResortInfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Attachment;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $resortInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['room.roomType', 'bookingRooms.room.roomType', 'additionalGuests', 'payments']);
        $this->resortInfo = ResortInfo::first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation - ' . ($this->resortInfo->resort_name ?? 'Tufan Convention Resort') . ' #' . str_pad($this->booking->id, 5, '0', STR_PAD_LEFT),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        try {
            $pdf = Pdf::loadView('admin.bookings.reservation-letter-template', [
                'booking' => $this->booking,
                'resortInfo' => $this->resortInfo,
            ]);

            return [
                Attachment::fromData(fn () => $pdf->output(), 'reservation-' . str_pad($this->booking->id, 5, '0', STR_PAD_LEFT) . '.pdf')
                    ->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF attachment for booking confirmation: ' . $e->getMessage());
            return [];
        }
    }
}
