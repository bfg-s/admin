<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class which is responsible for saving nested data or sorting.
 *
 * @property-read string|Model $model
 * @property-read int $depth
 * @property-read array $data
 * @property-read string|null $parent_field
 * @property-read string $order_field
 */
class NestableSaveRequest extends FormRequest
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
            'depth' => 'required|int',
            'data' => 'required|array',
            'parent_field' => 'required|string',
            'order_field' => 'required|string',
        ];
    }
}
