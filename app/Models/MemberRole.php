<?php

namespace App\Models;

class MemberRole extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'permission' => 'array'
    ];

    public function names()
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
            ->where('table_field', 'name')
            ->where('table_name', 'member_roles');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'fs_member_role_rels', 'member_id', 'role_id');
    }
}
