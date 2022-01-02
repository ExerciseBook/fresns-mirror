<?php

namespace App\Fresns\Panel\Http\Requests;

class UpdateSessionKeyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'platform_id' => 'required|int',
            'name' => 'required|string',
            'type' => 'required|int',
            'is_enable' => 'required|boolean',
            'plugin_unikey' => 'exists:App\Models\Plugin,unikey',
        ];
    }

    public function attributes()
    {
        return [
            'platform_id' => __('panel::panel.platform'),
            'name' => __('panel::panel.name'),
            'type' => __('panel::panel.type'),
            'is_enable' => __('panel::panel.status'),
            'plugin_unikey' => __('panel::panel.associatePlugin'),
        ];
    }
}
