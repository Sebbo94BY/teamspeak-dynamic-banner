<?php

namespace App\Http\Requests;

use App\Models\User;
use DateTimeZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'timezone' => ($this->timezone == 'null') ? null : $this->timezone,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'localization_id' => ['required', 'integer', 'exists:App\Models\Localization,id'],
            'timezone' => ['nullable', 'string', 'in:'.implode(',', timezone_identifiers_list(DateTimeZone::ALL))],
        ];
    }
}
