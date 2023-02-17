<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'approval_admin_id' => 'required|integer|exists:admins,id',

        ];

    }
    public function messages()
    {
        return [
            'user_id.required' => 'User id is required',
            'user_id.integer' => 'User id must be an integer',
            'user_id.exists' => 'User id does not exist',
            'first_name.required' => 'First name is required',
            'first_name.string' => 'First name must be a string',
            'last_name.required' => 'Last name is required',
            'last_name.string' => 'Last name must be a string',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'approval_admin_id.required' => 'Approval admin id is required',
            'approval_admin_id.integer' => 'Approval admin id must be an integer',
            'approval_admin_id.exists' => 'Approval admin id does not exist',
        ];
    }
}
