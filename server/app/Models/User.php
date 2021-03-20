<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'pubkey', 'description'];

    // Users can have many files
    public function files()
    {
        return $this->hasMany('App\Models\File', 'uploaded_by_user');
    }

    // Users can have many galleries
    public function galleries()
    {
        return $this->hasMany('App\Models\Gallery', 'created_by_user');
    }
}
