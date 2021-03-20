<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['title', 'description', 'mime_type', 'gallery_id', 'uploaded_by_user', 'uploaded_by_ip', 'file_path', 'url_path', 'original_file_name', 'hash', 'read_permission'];

    // Files are owned by users
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'uploaded_by_user');
    }

    // Files belong to galleries
    public function gallery()
    {
        return $this->belongsTo('App\Models\Gallery', 'id', 'uploaded_by_user');
    }
}