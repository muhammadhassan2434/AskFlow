<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
     protected $fillable = [
        'workspace_id',
        'uploaded_by',
        'title',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'status',
        'processed_at',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
