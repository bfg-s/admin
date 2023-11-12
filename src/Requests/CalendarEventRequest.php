<?php

namespace Admin\Requests;

use Admin\Facades\AdminFacade;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read int|null $id
 * @property-read string $title
 * @property-read string|null $description
 * @property-read string|null $url
 * @property-read string $start
 * @property-read string|null $end
 */
class CalendarEventRequest extends FormRequest
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
            'id' => 'int',
            'title' => 'string',
            'description' => 'string',
            'url' => 'string|nullable',
            'start' => 'required|string',
            'end' => 'string',
        ];
    }
}
