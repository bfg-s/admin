<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\AdminFacade;
use Illuminate\Foundation\Http\FormRequest;

/**
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
            'id' => 'required|int',
            'field_name' => 'required|string',
            'val' => 'required',
        ];
    }
}
