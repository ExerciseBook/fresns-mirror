<?php

namespace App\Fresns\Panel\Http\Requests;

class UpdatePolicyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_terms_close' => 'required',
            'account_privacy_close' => 'required',
            'account_cookie_close' => 'required',
            'account_delete_close' => 'required',
            'delete_account' => 'required'
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
