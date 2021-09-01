<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsCommentLogs;

use App\Base\Models\BaseAdminModel;
use Illuminate\Support\Facades\DB;

class AmModel extends BaseAdminModel
{
    protected $table = AmConfig::CFG_TABLE;

    // Front-end form field mapping
    public function formFieldsMap()
    {
        return AmConfig::FORM_FIELDS_MAP;
    }

    // New search criteria
    public function getAddedSearchableFields()
    {
        return AmConfig::ADDED_SEARCHABLE_FIELDS;
    }

    // hook - after adding
    public function hookStoreAfter($id)
    {
    }

    public function getRawSqlQuery()
    {
        $query = DB::table(AmConfig::CFG_TABLE) ->where('deleted_at', null);
        $request = request();
        // 1.Draft + review rejection (status=1+4)
        // 2.Under Review (status=2)
        $status = $request->input('status');
        if ($status == 1) {
            $query->where('status', 1)->orwhere('status', 4);
        } else {
            $query->where('status', 2);
        }
        $class = $request->input('class');
        if ($class == 1) {
            $query->where('comment_id', null);
        } else {
            $query->where('comment_id', '!=', null);
        }
        $query->orderBy('id', 'desc');

        return $query;
    }

    // Search for sorted fields
    public function initOrderByFields()
    {
        $orderByFields = [
            'id' => 'DESC',
            // 'updated_at' => 'DESC',
        ];

        return $orderByFields;
    }
}
