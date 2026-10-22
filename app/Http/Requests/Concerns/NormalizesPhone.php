<?php

namespace App\Http\Requests\Concerns;

/**
 * Accepts "0917 123 4567", "0917-123-4567" or "+63 917 123 4567"
 * and stores the PH mobile number as 09XXXXXXXXX.
 */
trait NormalizesPhone
{
    public const PHONE_RULE = 'regex:/^09\d{9}$/';

    protected function normalizePhone(): void
    {
        if (! is_string($this->input('phone'))) {
            return;
        }

        $digits = preg_replace('/\D/', '', $this->input('phone'));

        if (str_starts_with($digits, '639') && strlen($digits) === 12) {
            $digits = '0'.substr($digits, 2);
        }

        $this->merge(['phone' => $digits]);
    }

    protected function phoneMessages(): array
    {
        return [
            'phone.regex' => 'Enter a Philippine mobile number in the format 09XXXXXXXXX.',
        ];
    }
}
