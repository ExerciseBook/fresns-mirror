<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Emoji;
use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateEmojiGroupRequest;

class EmojiGroupController extends Controller
{
    public function index()
    {
        $groups = Emoji::group()
            ->orderBy('rank_num')
            ->with('names')
            ->with(['emojis' => function($query) {
                $query->orderBy('rank_num');
            }])
            ->get();

        return view('panel::operation.emoji', compact('groups'));
    }

    public function store(Emoji $emojiGroup, UpdateEmojiGroupRequest $request)
    {
        $emojiGroup->rank_num = $request->rank_num;
        $emojiGroup->code = $request->code;
        $emojiGroup->is_enable = $request->is_enable;
        $emojiGroup->image_file_url = $request->image_file_url ?: '';
        $emojiGroup->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $emojiGroup->type = 2;
        $emojiGroup->save();

        foreach ($request->names as $langTag => $content) {
            $language = Language::tableName('emojis')
                ->where('table_id', $emojiGroup->id)
                ->where('lang_tag', $langTag)
                ->first();

            if (!$language) {
                // create but no content
                if (!$content) {
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'emojis',
                    'table_field' => 'name',
                    'table_id' => $emojiGroup->id,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }
        return $this->createSuccess();
    }


    public function update(Emoji $emojiGroup, UpdateEmojiGroupRequest $request)
    {
        $emojiGroup->rank_num = $request->rank_num;
        $emojiGroup->code = $request->code;
        $emojiGroup->is_enable = $request->is_enable;
        $emojiGroup->image_file_url = $request->image_file_url ?: '';
        $emojiGroup->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $emojiGroup->save();

        foreach ($request->names as $langTag => $content) {
            $language = Language::tableName('emojis')
                ->where('table_id', $emojiGroup->id)
                ->where('lang_tag', $langTag)
                ->first();

            if (!$language) {
                // create but no content
                if (!$content) {
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'emojis',
                    'table_field' => 'name',
                    'table_id' => $emojiGroup->id,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }
        return $this->updateSuccess();
    }

    public function destroy(Emoji $emojiGroup)
    {
        $emojiGroup->delete();

        return $this->deleteSuccess();
    }

}
