<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\AdminFacade;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read int $id
 * @property-read string $field
 * @property-read string $model
 * @property-read array $fileList
 */
class SaveImageOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !AdminFacade::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|int',
            'field' => 'required|string',
            'model' => 'required|string',
            'fileList' => 'required|array',
        ];
    }
}
