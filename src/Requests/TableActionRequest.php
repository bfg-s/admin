<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\Admin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request class which is responsible for the action of the model table.
 *
 * @property-read Model|string $class
 * @property-read array $ids
 * @property-read array $columns
 */
class TableActionRequest extends FormRequest
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
            'class' => 'required|string',
            'ids' => 'required|array',
            'columns' => 'required|array',
            'orderBy' => 'required|string',
            'orderType' => 'required|string',
        ];
    }
}
