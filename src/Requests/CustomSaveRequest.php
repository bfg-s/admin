<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class which is responsible for data validation during custom saving of model data.
 *
 * @property-read string $model
 * @property-read int $id
 * @property-read string $field_name
 * @property-read string $val
 */
class CustomSaveRequest extends FormRequest
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
            'id' => 'required|int',
            'field_name' => 'required|string',
            'val' => 'required',
        ];
    }
}
