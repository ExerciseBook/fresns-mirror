<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class LanguagePackController extends Controller
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

        return view('panel::client.languages', compact(
            'languages',
            'defaultLanguage',
            'status',
            'codes',
            'continents'
        ));
    }


    public function show($id)
    {
        return view('panel::client.languages_config');
    }

    public function store($id)
    {
        return $this->createSuccess();
    }

    public function destroy($id, $configId)
    {
        return back();
    }
}
