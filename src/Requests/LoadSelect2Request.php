<?php

namespace Admin\Requests;

use Admin\Facades\AdminFacade;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $_select2_name
 */
class LoadSelect2Request extends FormRequest
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
            '_select2_name' => 'required|string',
        ];
    }
}
