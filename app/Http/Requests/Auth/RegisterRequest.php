<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    use NormalizesPhone;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', self::PHONE_RULE],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(['customer', 'vendor'])],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->phoneMessages(),
            'email.unique' => 'An account with this email already exists.',
            'role.in' => 'Choose whether you are signing up as a customer or a vendor.',
            'terms.accepted' => 'Please agree to the Terms of Service.',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'full name', 'phone' => 'mobile number'];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizePhone();

        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }
}
