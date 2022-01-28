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
            'member_multiple' => 'string',
            'multi_member_service' => 'nullable|string',
            'multi_member_roles' => 'array',
            'default_role' => 'int',
             //'default_avatar' => 'required|int',
             //'anonymous_avatar' => 'required|int',
             //'deactivate_avatar' => 'required|int',
            'password_length' => 'int',
            'password_strength' => 'array',
            'mname_min' => 'int',
            'mname_max' => 'int',
            'mname_edit' => 'int',
            'nickname_edit' => 'int',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
