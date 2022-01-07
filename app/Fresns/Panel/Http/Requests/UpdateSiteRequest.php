<?php

namespace App\Fresns\Panel\Http\Requests;

class UpdateSiteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'site_domain' => 'url|nullable',
            'site_name' => 'array|nullable',
            'site_desc' => 'array|nullable',
            'site_copyright' => 'string|nullable',
            'site_copyright_years' => 'string|nullable',
            'default_timezone' => 'string|nullable',
            'site_mode' => 'string|nullable',
            'site_public_close' => 'string|nullable',
            'site_public_service' => 'string|nullable',
            'site_register_email' => 'string|nullable',
            'site_register_phone' => 'string|nullable',
            'site_private_close' => 'string|nullable',
            'site_private_service' => 'string|nullable',
            'site_private_end' => 'string|nullable',
            'site_email' => 'email|nullable',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
