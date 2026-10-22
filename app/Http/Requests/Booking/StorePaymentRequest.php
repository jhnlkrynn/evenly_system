<?php

namespace App\Http\Requests\Booking;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pay', $this->route('booking'));
    }

    public function rules(): array
    {
        $allowed = array_keys($this->route('booking')->paymentOptions());

        return [
            'method' => ['required', Rule::in(Payment::METHODS)],
            'type' => ['required', Rule::in($allowed)],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' => 'Choose GCash, Maya, or Cash.',
            'type.in' => 'That payment type isn’t available for this booking any more.',
        ];
    }
}
