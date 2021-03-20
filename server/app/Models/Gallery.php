<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['url', 'title', 'description', 'created_by_user', 'created_by_ip', 'read_permission', 'write_permission'];

    // Galleries are cated by a user
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'created_by_user');
    }

    // Galleries have many files
    public function files()
    {
        return $this->hasMany('App\Models\File');
    }
}
