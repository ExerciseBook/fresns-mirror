<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Http\Request;

class IframeController extends Controller
{
    protected function addLangToUrl($url)
    {
        // Parse the passed url
        $queryString = parse_url($url);
        // Get the query parameters in the url separately
        $query = $queryString['query'] ?? '';

        // Converting query parameters into arrays
        parse_str($query, $params);

        // Passing on the language tag
        $langParams = array_merge([
            'lang' => \App::getLocale(),
        ], $params);

        // Splicing query parameters
        $langQueryString = http_build_query($langParams);

        // Splicing url
        if (! empty($queryString['path'])) {
            $langUrl = $queryString['path'].'?'.$langQueryString;
        } else {
            $langUrl = $url.'?'.$langQueryString;
        }

        return $langUrl;
    }

    // app center iframe
    public function iframe(Request $request)
    {
        // Sidebar
        $enablePlugins = Plugin::type(1)->isEnable()->get();

        $url = $this->addLangToUrl($request->url);

        return view('FsView::extensions.iframe', compact('url', 'enablePlugins'));
    }
}
