<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class which is responsible for dashboard save.
 */
class SaveDashboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !Admin::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dashboard_id' => 'required|int',
            'lines.*.*.id' => 'required|numeric',
            'lines.*.*.class' => 'required|string',
            'lines.*.*.settings' => 'array',
        ];
    }
}
