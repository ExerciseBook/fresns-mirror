<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateLanguageRequest;

class LanguageController extends Controller
{
    public function update($itemKey, Request $request)
    {
        $language = Language::ofConfig()
            ->where('table_key', $itemKey)
            ->where('lang_tag', $request->lang_tag)
            ->first();

        if (!$language) {
            // create but no content
            $language = new Language();
            $language->fill([
                'table_name' => 'configs',
                'table_field' => 'item_value',
                'table_key' => $itemKey,
                'lang_tag' => $request->lang_tag,
            ]);
        }

        $language->lang_content = $request->content;
        $language->save();

        return $this->updateSuccess();
    }

    public function batchUpdate($itemKey, Request $request)
    {
        $configContent = null;

        foreach($request->languages as $langTag => $content) {
            $language = Language::ofConfig()
                ->where('table_key', $itemKey)
                ->where('lang_tag', $langTag)
                ->first();
            if (!$language) {
                // create but no content
                if (!$content){
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'configs',
                    'table_field' => 'item_value',
                    'table_key' => $itemKey,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }

        if ($request->update_config || $request->sync_config) {
            $key = $request->update_config ?: $itemKey;
            $config = Config::where('item_key', $key)->first();
            $content = $request->languages[$this->defaultLanguage] ?? current(array_filter($request->languages));

            if ($config && $content) {
                $config->item_value = $content;
                $config->save();
            }
        }

        return $this->updateSuccess();
    }
}
