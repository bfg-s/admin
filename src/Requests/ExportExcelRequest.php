<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class which is responsible for data validation when exporting to Excel or CSV.
 *
 * @property-read string $model
 * @property-read array $ids
 * @property-read string $order
 * @property-read string $order_type
 * @property-read string $table
 */
class ExportExcelRequest extends FormRequest
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
            'model' => 'required|string',
            'ids' => 'nullable|array',
            'order' => 'required|string',
            'order_type' => 'required|string',
            'table' => 'required|string',
        ];
    }
}
