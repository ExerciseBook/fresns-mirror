<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Config;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultLanguage;

    public function __construct()
    {
        View::share('langs', config('panel.langs'));

        try {
            // 默认语言
            $defaultLanguageConfig = Config::where('item_key','default_language')->first();

            $defaultLanguage = $defaultLanguageConfig ? $defaultLanguageConfig->item_value : 'zh-hans';
            $this->defaultLanguage = $defaultLanguage;
            View::share('defaultLanguage', $defaultLanguage);


            // 可选的语言
            $languageConfig = Config::where('item_key', 'language_menus')->first();
            $optionalLanguages = $languageConfig ? $languageConfig->item_value : [];
            View::share('optionalLanguages', collect($optionalLanguages));

            $areaCodeConfig = Config::where('item_key', 'area_codes')->first();
            $areaCodes = $areaCodeConfig ? $areaCodeConfig->item_value : [];
            View::share('areaCodes', collect($areaCodes));
        } catch(\Exception $e) {}
    }

    public function createSuccess()
    {
        return $this->successResponse('create');
    }

    public function updateSuccess()
    {
        return $this->successResponse('update');
    }

    public function deleteSuccess()
    {
        return $this->successResponse('delete');
    }

    public function successResponse($action)
    {
        return request()->ajax()
            ? response()->json(['message' => __('panel::panel.'.$action.'Success')], 200)
            : back()->with('success', __('panel::panel.'.$action.'Success'));
    }
}
