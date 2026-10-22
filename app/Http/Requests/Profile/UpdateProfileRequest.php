<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    use NormalizesPhone;

    /** Error bag, so this form's errors don't show on the password form. */
    protected $errorBag = 'profile';

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', self::PHONE_RULE],
        ];
    }

    public function messages(): array
    {
        return $this->phoneMessages();
    }

    public function attributes(): array
    {
        return ['name' => 'full name', 'phone' => 'mobile number'];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();
    }
}
