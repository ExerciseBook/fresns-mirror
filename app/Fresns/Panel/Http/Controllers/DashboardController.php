<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\User;
use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
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

        $configKeys = [
            'user_counts',
            'member_counts',
            'group_counts',
            'hashtag_counts',
            'post_counts',
            'comment_counts',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $keyCount = SessionKey::count();
        $adminCount = User::ofAdmin()->count();
        $plugins = Plugin::all();

        return view('panel::dashboard', compact('news', 'params', 'keyCount', 'adminCount', 'plugins'));
    }
}
