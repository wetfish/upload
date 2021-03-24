<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\File;

class Gallery extends Model
{
    protected $fillable = ['url', 'title', 'description', 'read_permission', 'write_permission'];

    // Galleries are cated by a user
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'created_by_user');
    }

    // Galleries have many files
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
