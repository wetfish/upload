<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gallery;

class File extends Model
{
    protected $fillable = ['title', 'description', 'read_permission'];

    // Files are owned by users
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'uploaded_by_user');
    }

    // Files belong to galleries
    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'id', 'uploaded_by_user');
    }
}
