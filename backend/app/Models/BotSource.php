<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotSource extends Model
{
    protected $fillable = ['bot_id', 'type', 'title', 'content', 'url', 'file_name', 'file_path', 'file_type', 'file_size', 'status', 'error_message', 'meta',];
    protected $casts = ['meta' => 'array',];
    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
   
}
