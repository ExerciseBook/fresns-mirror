<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show()
    {
        $news = \Cache::remember('news', 86400, function() {
            $newUrl = config('panel.news_url');
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $newUrl);
            $news = json_decode($response->getBody(), true);
            return $news;
        });
        $news = collect($news)->where('langTag', \App::getLocale())->first();

        return view('panel::dashboard', compact('news'));
    }
}
