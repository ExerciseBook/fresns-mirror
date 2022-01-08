<?php

namespace App\Fresns\Panel\Http\Requests;

class UpdateUserConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'connects' => 'array',
            'connect_plugins' => 'array',
            'account_prove_service' => 'nullable|string',
            'member_multiple' => 'required|string',
            'multi_member_service' => 'nullable|string',
            'multi_member_roles' => 'array',
            'default_role' => 'required|int',
            // //'default_avatar' => 'required|int',
            // //'anonymous_avatar' => 'required|int',
            // //'deactivate_avatar' => 'required|int',
            'password_length' => 'required|int',
            'password_strength' => 'required|array',
            'mname_min' => 'required|int',
            'mname_max' => 'required|int',
            'mname_edit' => 'required|int',
            'nickname_edit' => 'required|int',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
