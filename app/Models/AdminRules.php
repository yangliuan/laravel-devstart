<?php

namespace App\Models;

class AdminRules extends BaseModel
{
    protected $table = 'admin_rules';

    protected $fillable = [
        'pid',
        'name',
        'icon',
        'api_http_method',
        'api_behavior',
        'params',
        'gui_type',
        'gui_behavior',
        'status',
        'is_log',
        'sort',
    ];

    protected $dates = [];

    protected $casts = [];

    protected $appends = [];

    protected $parentColumn = 'pid';

    public function group()
    {
        return $this->belongsToMany('App\Models\AdminGroups', 'admin_group_rules', 'rule_id', 'group_id');
    }

    public function children()
    {
        return $this->hasMany($this, 'pid', 'id');
    }

    public function toTree(array $rules = [], $parentId = 0, $select_field = ['id','pid','name','icon','gui_type','gui_behavior'])
    {
        $branch = [];

        if (empty($rules)) {
            $rules = static::where('status', 1)->get($select_field)->toArray();
        }

        foreach ($rules as $rule) {
            if ($rule[$this->parentColumn] == $parentId) {
                $children = $this->toTree($rules, $rule[$this->getKeyName()]);

                if ($children) {
                    $rule['children'] = $children;
                }

                $branch[] = $rule;
            }
        }

        return collect($branch)->sortBy('sort')->values()->all();
    }

    public function adminTree($select_field = ['id','pid','name','icon','gui_type','gui_behavior'])
    {
        if (config('adminrbac.develop_model')) {
            $rules = static::all($select_field)->toArray();
        } else {
            $rules = static::where('id', '<>', 17)->where('pid', '<>', 17)->get($select_field)->toArray();
        }

        return $this->toTree($rules);
    }
}
