<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateLanguageMenuRequest;
use App\Fresns\Panel\Http\Requests\UpdateLanguageRankRequest;
use App\Fresns\Panel\Http\Requests\UpdateDefaultLanguageRequest;

class LanguageMenuController extends Controller
{
    public function index()
    {
        $languageConfig = Config::where('item_key', 'language_menus')->firstOrFail();
        $languages = $languageConfig->item_value;

        $defaultLanguageConfig = Config::where('item_key', 'default_language')->firstOrFail();
        $defaultLanguage = $defaultLanguageConfig->item_value;

        $statusConfig = Config::where('item_key', 'language_status')->firstOrFail();
        $status = $statusConfig->item_value == 'true';

        $codeConfig = Config::where('item_key', 'language_codes')->firstOrFail();
        $codes = $codeConfig->item_value;

        $continentConfig = Config::where('item_key', 'continents')->firstOrFail();
        $continents = $continentConfig->item_value;

        return view('panel::system.languages', compact(
            'languages', 'defaultLanguage', 'status',
            'codes', 'continents'
        ));
    }

    public function switchStatus()
    {
        $statusConfig = Config::where('item_key', 'language_status')->firstOrFail();
        $statusConfig->item_value = $statusConfig->item_value == 'true' ? 'false' : 'true';
        $statusConfig->save();

        return $this->updateSuccess();
    }

    public function updateDefaultLanguage(UpdateDefaultLanguageRequest $request)
    {
        $defaultLanguageConfig = Config::where('item_key', 'default_language')->firstOrFail();
        $defaultLanguageConfig->item_value = $request->default_language;
        $defaultLanguageConfig->save();

        return $this->updateSuccess();
    }

    public function updateRank(UpdateLanguageRankRequest $request, $langTag)
    {
        $languageConfig = Config::where('item_key', 'language_menus')->firstOrFail();
        $languages = $languageConfig->item_value;

        $languageKey = collect($languages)->search(function($item) use ($langTag) {
            return $item['langTag'] == $langTag;
        });

        if (!$languageKey) {
            return back()->with('failear', __('panel::panel.languageNotExists'));
        }

        $language = $languages[$languageKey];
        $language['rankNum'] = $request->rank_num;

        $languages[$languageKey] = $language;
        $languageConfig->item_value = $languages;
        $languageConfig->save();

        return $this->updateSuccess();
    }

    public function store(UpdateLanguageRequest $request)
    {
        $codeConfig = Config::where('item_key', 'language_codes')->firstOrFail();
        $codes = $codeConfig->item_value;
        $code = collect($codes)->where('code', $request->lang_code)->firstOrFail();

        $languageConfig = Config::where('item_key', 'language_menus')->firstOrFail();
        $languages = $languageConfig->item_value;

        $langTag = $request->area_code ? $request->lang_code.'-'.$request->area_code : $request->lang_code;

        if (collect($languages)->where('langTag', $langTag)->first()) {
            return back()->with('failear', __('panel::panel.languageExists'));
        }

        $areaName = '';
        if ($request->area_code) {
            $areaCodeConfig = Config::where('item_key', 'area_codes')->firstOrFail();
            $areaCodes = $areaCodeConfig->item_value;

            $areaCode = collect($areaCodes)->where('code', $request->area_code)->firstOrFail();
            $areaName = $areaCode['name'];
        }

        $data = [
            'rankNum' => $request->rank_num,
            'langCode' => $request->lang_code,
            'langName' => $code['name'] ?? '',
            'langTag' => $langTag,
            'continentId' => $request->continent_id,
            'areaStatus' => $request->area_status ? 'true' : 'false',
            'areaCode' => $request->area_code,
            'areaName' => $areaName,
            'writingDirection' => $code['writingDirection'],
            'lengthUnits' =>  $request->length_units,
            'dateFormat' =>  $request->date_format,
            'timeFormatMinute' =>  $request->time_format_minute,
            'timeFormatHour' =>  $request->time_format_hour,
            'timeFormatDay' =>  $request->time_format_day,
            'timeFormatMonth' =>  $request->time_format_month,
            'packVersion' =>  1,
            'isEnable' =>  $request->is_enable ? 'true' : 'false',
        ];

        $languages[] = $data;
        $languageConfig->item_value = $languages;
        $languageConfig->save();

        return $this->createSuccess();
    }

    public function update(UpdateLanguageRequest $request, string $langTag)
    {
        $codeConfig = Config::where('item_key', 'language_codes')->firstOrFail();
        $codes = $codeConfig->item_value;
        $code = collect($codes)->where('code', $request->lang_code)->firstOrFail();

        $languageConfig = Config::where('item_key', 'language_menus')->firstOrFail();
        $languages = $languageConfig->item_value;

        // check exists
        $langTag = $request->area_code ? $request->lang_code.'-'.$request->area_code : $request->lang_code;
        if ($langTag != $request->old_lang_tag && collect($languages)->where('langTag', $langTag)->first()) {
            return back()->with('failear', __('panel::panel.languageExists'));
        }

        // default language
        $defaultLanguageConfig = Config::where('item_key', 'default_language')->firstOrFail();
        $defaultLanguage = $defaultLanguageConfig->item_value;

        $areaName = '';
        if ($request->area_code) {
            $areaCodeConfig = Config::where('item_key', 'area_codes')->firstOrFail();
            $areaCodes = $areaCodeConfig->item_value;

            $areaCode = collect($areaCodes)->where('code', $request->area_code)->firstOrFail();
            $areaName = $areaCode['name'];
        }

        $isDefault = $defaultLanguage == $langTag;

        $data = [
            'rankNum' => $request->rank_num,
            'langCode' => $request->lang_code,
            'langName' => $code['name'] ?? '',
            'langTag' => $langTag,
            'continentId' => $request->continent_id,
            'areaStatus' => $request->area_status ? 'true' : 'false',
            'areaCode' => $request->area_code,
            'areaName' => $areaName,
            'writingDirection' => $code['writingDirection'],
            'lengthUnits' =>  $request->length_units,
            'dateFormat' =>  $request->date_format,
            'timeFormatMinute' =>  $request->time_format_minute,
            'timeFormatHour' =>  $request->time_format_hour,
            'timeFormatDay' =>  $request->time_format_day,
            'timeFormatMonth' =>  $request->time_format_month,
            'packVersion' =>  1,
            'isEnable' =>  $request->is_enable ? 'true' : 'false',
        ];

        $languageKey = collect($languages)->search(function($item) use ($request) {
            return $item['langTag'] == $request->old_lang_tag;
        });

        if (!$languageKey) {
            return back()->with('failear', __('panel::panel.languageNotExists'));
        }

        $languages[$languageKey] = $data;
        $languageConfig->item_value = $languages;
        $languageConfig->save();

        if ($isDefault) {
            $languageConfig->item_value = $langTag;
            $languageConfig->save();
        }

        return $this->updateSuccess();
    }

    public function destroy(string $code)
    {
        $languageConfig = Config::where('item_key', 'language_menus')->firstOrFail();
        $languages = $languageConfig->item_value;

        $languages = collect($languages)->reject(function($language) use ($code) {
            return $language['langCode'] == $code;
        })->toArray();
        $languageConfig->item_value = $languages;
        $languageConfig->save();

        return back();
    }
}
