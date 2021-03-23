<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Challenge;
use App\Models\User;

class Key extends Model
{
    // Keys have many challenges
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    // Keys can belong to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
