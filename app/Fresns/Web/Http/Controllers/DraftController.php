<?php
namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Exceptions\ErrorException;
use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DraftController extends Controller
{
    public function index()
    {
        $type =  request('type', 'post');
        $clid = null;

        // 获取草稿列表
        $drafts = Arr::get(ApiHelper::make()->get("/api/v2/editor/{$type}/drafts")->toArray(), 'data.list');

        if (count($drafts) === 0) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                ]
            ])->toArray();

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message']);
            }

            return redirect()->route('fresns.drafts.edit', $response['data']['detail']['id']);

        }

        $draftInfo = self::getDraft($type);

        $config = $draftInfo['config'];
        $stickers = $draftInfo['stickers'];

        return view('drafts.index', compact('drafts','type', 'clid', 'config', 'stickers'));
    }

    public function edit(int $draftId)
    {
        $type = request('type', 'post');

        $clid = null;

        $plid = $draftId;

        $draftInfo = self::getDraft($type, $draftId);

        $config = $draftInfo['config'];
        $stickers = $draftInfo['stickers'];
        $draft = $draftInfo['draft'];

        return view('drafts.edit', compact('draft','type', 'clid', 'plid', 'config', 'stickers'));
    }

    public function store()
    {
        $type = request('type', 'post');
        $fsid = request('fsid');

        if ($fsid) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/generate/{$fsid}")->toArray();
        } else {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                ]
            ])->toArray();
        }

        if (data_get($response, 'code') !== 0) {
            throw new ErrorException($response['message']);
        }

        return redirect()->route('fresns.drafts.edit', $response['data']['detail']['id']);

    }

    public static function getDraft(string $type, ?int $draftId = null)
    {
        $client = ApiHelper::make();

        if (empty($draftId)) {
            $results = $client->handleUnwrap([
                'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
                'stickers' => $client->getAsync('/api/v2/global/stickers'),
            ]);

            $draftInfo['draft'] = null;
        } else {
            $results = $client->handleUnwrap([
                'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
                'stickers' => $client->getAsync('/api/v2/global/stickers'),
                'draft' => $client->getAsync("/api/v2/editor/post/{$draftId}"),
            ]);

            if ($results['draft']['code'] !== 0) {
                throw new ErrorException($results['draft']['message'], $results['draft']['code']);
            }

            $draftInfo['draft'] = $results['draft']['data'];
        }

        $draftInfo['config'] = $results['config']['data'];
        $draftInfo['stickers'] = $results['stickers']['data'];

        return $draftInfo;
    }

    public function update(Request $request, int $draftId)
    {
        $response = ApiHelper::make()->put("/api/v2/editor/{$request->post('type')}/{$draftId}", [
            'json' => [
                'postGid' => $request->post('gid'),
                'postTitle' => $request->post('postTitle'),
                'content' => $request->post('content'),
                'isAnonymous' => $request->post('anonymous')
            ]
        ]);

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        $response = ApiHelper::make()->post("/api/v2/editor/{$request->post('type')}/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return redirect()->route('fresns.post.list')->with('success', $response['message']);
    }

    public function destroy(Request $request, string $draftId)
    {
        $response = ApiHelper::make()->delete("/api/v2/{$request->get('type')}/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }
}