<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;
use App\Models\Gallery;

class User extends Model
{
    protected $fillable = ['name', 'email', 'pubkey', 'description'];

    // Users can have many files
    public function files()
    {
        return $this->hasMany(File::class, 'uploaded_by_user');
    }

    // Users can have many galleries
    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'created_by_user');
    }
}
