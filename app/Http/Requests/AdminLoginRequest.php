<?php

namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AdminLoginRequest
 * @package Admin\Http\Requests
 */
class AdminLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Admin::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => 'required|min:3|max:191',
            'password' => 'required|min:4|max:191',
        ];
    }
}
