<?php

namespace App\Models;

class AdminGroups extends BaseModel
{
    protected $table = 'admin_groups';

    protected $fillable = [
        'title',
        'desc',
        'status',
        'cache',
    ];

    protected $dates = [];

    protected $casts = [
        'cache' => 'json',
    ];

    protected $appends = [];

    protected $hidden = [];

    public function rule()
    {
        return $this->belongsToMany('App\Models\AdminRules', 'admin_group_rules', 'group_id', 'rule_id');
    }

    public function admin()
    {
        return $this->hasMany('App\Models\Admin', 'group_id', 'id');
    }

    public function refreshCache()
    {
        $rules = AdminRules::select('id', 'pid', 'name', 'gui_type', 'gui_behavior')
            ->whereHas('group', function ($query) {
                $query->where('admin_groups.id', $this->id);
            })
            ->get()
            ->toArray();

        return $this->update([
            'cache' => (new AdminRules())->toTree($rules),
        ]);
    }
}
