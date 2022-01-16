<?php

namespace App\Fresns\Panel\Http\Requests;

class UpdateLanguageMenuRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rank_num' => 'required|string',
            'lang_code' => 'required|string',
            'old_lang_tag' => 'required|string',
            'continent_id' => 'int',
            'area_code' => 'string',
            'area_status' => 'required|boolean',
            'length_units' => 'required|string',
            'date_format' => 'required|string',
            'time_format_minute' => 'required|string',
            'time_format_hour' => 'required|string',
            'time_format_day' => 'required|string',
            'time_format_month' => 'required|string',
            'is_enable' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
