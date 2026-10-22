<?php

namespace App\Http\Requests\Booking;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    private ?Service $service = null;

    public function authorize(): bool
    {
        return $this->user()?->hasRole('customer') ?? false;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'event_type' => ['required', Rule::in(Booking::EVENT_TYPES)],
            'event_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addYears(2)->toDateString()],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'venue' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', Rule::exists('barangays', 'name')],
            'pax' => ['required', 'integer', 'min:10', 'max:2000'],
            'needs_decoration' => ['required', 'boolean'],
            'serving_count' => ['required', 'integer', 'min:0', 'max:50'],
            'dishwashing_count' => ['required', 'integer', 'min:0', 'max:30'],
            'theme' => ['nullable', Rule::in(Booking::THEMES)],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['nullable', Rule::in(['GCash', 'Maya', 'Cash'])],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.required' => 'Pick a date for your event.',
            'event_date.after_or_equal' => 'That date has already passed. Please choose an upcoming date.',
            'end_time.after' => 'The event must end after it starts.',
            'barangay.required' => 'Choose the barangay where your event will be held.',
            'barangay.exists' => 'Choose a barangay in Nasugbu from the list.',
            'pax.min' => 'Bookings start at 10 guests.',
            'terms.accepted' => 'Please agree to the booking terms.',
        ];
    }

    public function attributes(): array
    {
        return ['event_type' => 'occasion', 'event_date' => 'event date', 'pax' => 'number of guests', 'venue' => 'venue'];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->hasAny(['service_id', 'event_date'])) {
                return;
            }

            $service = $this->service();
            if (! $service || ! $service->is_active || ! $service->vendor->isListed()) {
                $validator->errors()->add('service_id', 'This service is no longer available. Please choose another one.');

                return;
            }

            if (! $this->boolean('needs_decoration') && ! $this->integer('serving_count') && ! $this->integer('dishwashing_count')) {
                $validator->errors()->add('needs_decoration', 'Choose at least one service: decoration, serving staff, or dishwashing.');
            }

            if (Booking::vendorIsBookedOn($service->vendor_id, $this->input('event_date'))) {
                $validator->errors()->add('event_date', $service->vendor->business_name.' is already booked on '
                    .Carbon::parse($this->input('event_date'))->format('M j, Y').'. Please choose another date.');
            }
        }];
    }

    public function service(): ?Service
    {
        return $this->service ??= Service::with('vendor.user', 'category')->find($this->integer('service_id'));
    }
}
