<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Emoji;
use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateSiteRequest;

class EmojiGroupController extends Controller
{
    public function index()
    {
        $groups = Emoji::group()->with('emojis', 'names')->get();

        return view('panel::operation.emoji', compact('groups'));
    }

    public function update(Request $request)
    {
        return $this->updateSuccess();
    }

    public function destroy(Emoji $emojiGroup)
    {
        $emojiGroup->delete();

        return $this->deleteSuccess();
    }
}
