<?php

declare(strict_types=1);

namespace Admin\Requests;

use Admin\Facades\AdminFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
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
            'class' => 'required|string',
            'ids' => 'required|array',
            'columns' => 'required|array',
            'orderBy' => 'required|string',
            'orderType' => 'required|string',
        ];
    }
}
