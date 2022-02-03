<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Emoji;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateEmojiRequest;

class EmojiController extends Controller
{
    public function store(Emoji $emoji, UpdateEmojiRequest $request)
    {
        $emoji->parent_id = $request->parent_id;
        $emoji->rank_num = $request->rank_num;
        $emoji->code = $request->code;
        $emoji->name = $request->code;
        $emoji->is_enable = $request->is_enable;
        $emoji->image_file_url = $request->image_file_url ?: '';
        $emoji->type = 1;
        $emoji->save();

        return $this->createSuccess();
    }

    public function update(Emoji $emoji, Request $request)
    {
        $emoji->is_enable = $request->is_enable;
        $emoji->save();

        return $this->updateSuccess();
    }

    public function destroy(Emoji $emoji)
    {
        $emoji->delete();

        return $this->deleteSuccess();
    }

    public function updateRank(Emoji $emoji, Request $request)
    {
        $emoji->rank_num = $request->rank_num;
        $emoji->save();

        return $this->updateSuccess();
    }

    public function batchUpdate(Request $request)
    {
        $group = Emoji::group()->where('id', $request->parent_id)->firstOrFail();

        $emojis = $group->emojis;
        $deleteIds = $emojis->pluck('id')->diff(array_keys($request->rank_num));
        if ($deleteIds->count()) {
            $group->emojis()->whereIn('id', $deleteIds)->delete();
        }

        foreach($request->rank_num ?? [] as $id => $rank) {
            $emoji = $emojis->where('id', $id)->first();
            if (!$emoji) {
                continue;
            }
            $emoji->rank_num = $rank;
            $emoji->is_enable = $request->enable[$id] ?? 0;
            $emoji->save();
        }

        return $this->updateSuccess();
    }
}
