<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $fillable = ['workspace_id', 'name', 'slug', 'description', 'system_prompt', 'model', 'status',];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
    public function sources()
    {
        return $this->hasMany(BotSource::class);
    }
}
