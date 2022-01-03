<?php

namespace App\Fresns\Panel\Http\Requests;

class UpdateConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'domain' => 'url',
            'path' => 'string',
        ];
    }

    public function attributes()
    {
        return [
            'domain' => __('panel::panel.backendDomain'),
            'path' => __('panel::panel.safePath'),
        ];
    }
}
