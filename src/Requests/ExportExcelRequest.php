<?php

namespace Admin\Requests;

use Admin\Facades\AdminFacade;
use Illuminate\Foundation\Http\FormRequest;

/**
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
        return ! AdminFacade::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'model' => 'required|string',
            'ids' => 'required|array',
            'order' => 'required|string',
            'order_type' => 'required|string',
            'table' => 'required|string',
        ];
    }
}
