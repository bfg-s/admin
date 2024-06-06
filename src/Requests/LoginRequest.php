<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class which is responsible for admin login.
 *
 * @property-read string $login
 * @property-read string $password
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Admin::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string|min:3|max:191',
            'password' => 'required|string|min:4|max:191',
            'remember' => 'nullable|in:on,null'
        ];
    }
}
