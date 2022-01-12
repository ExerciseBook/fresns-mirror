<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use App\Models\StopWord;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateStopWordRequest;

class StopWordController extends Controller
{
    public function index(Request $request)
    {
        $words = StopWord::query();
        if ($keyword = $request->keyword) {
            $words->where('word', 'like', '%'.$keyword.'%');
        }

        $words = $words->latest()->paginate();

        $contentModeLabels = [
            1 => __('panel::panel.doNotHandle'),
            2 => '自动替换',
            3 => '禁止发表',
            4 => '发表后需审核',
        ];

        $memberModeLabels = $dialogModeLabels = [
            1 => __('panel::panel.doNotHandle'),
            2 => '自动替换',
            3 => '禁止发表',
        ];

        return view('panel::operation.stopWord', compact(
            'words', 'contentModeLabels', 'memberModeLabels',
            'dialogModeLabels', 'keyword'
        ));
    }

    public function store(StopWord $stopWord, UpdateStopWordRequest $request)
    {
        $stopWord->fill($request->all());
        $stopWord->save();

        return $this->createSuccess();
    }

    public function update(StopWord $stopWord, UpdateStopWordRequest $request)
    {
        $stopWord->update($request->all());
        return $this->updateSuccess();
    }

    public function destroy(StopWord $stopWord)
    {
        $stopWord->delete();
        return $this->deleteSuccess();
    }
}
