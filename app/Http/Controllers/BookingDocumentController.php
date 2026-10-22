<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\BookingPricing;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

/** Booking slip and invoice/receipt PDFs (barryvdh/laravel-dompdf). */
class BookingDocumentController extends Controller
{
    public function slip(Booking $booking): Response
    {
        $this->authorize('downloadDocuments', $booking);

        $booking->load(['customer', 'vendor', 'service.category', 'staff']);

        return Pdf::loadView('pdf.booking-slip', ['booking' => $booking])
            ->setPaper('a4')
            ->download("{$booking->booking_code}-booking-slip.pdf");
    }

    public function invoice(Booking $booking): Response
    {
        $this->authorize('downloadDocuments', $booking);

        $booking->load(['customer', 'vendor', 'service', 'payments' => fn ($q) => $q->oldest()]);

        return Pdf::loadView('pdf.invoice', [
            'booking' => $booking,
            'lines' => BookingPricing::lines(
                $booking->needs_decoration,
                $booking->serving_count,
                $booking->dishwashing_count,
                (float) $booking->total_amount,
            ),
        ])->setPaper('a4')->download("{$booking->booking_code}-invoice.pdf");
    }
}
