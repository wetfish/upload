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

    // Helper function to generate unique filenames
    public static function uniqueName($extension)
    {
        // Estimate the total number of files uploaded based on the largest file ID
        $newest = self::orderByDesc('id')->first();
        $length = strlen($newest->id);

        // Enforce a minimum filename length of 2, 1 character long filenames are weird
        if($length < 2) {
            $length = 2;
        }

        // Generate a random filename with a maximum length based on the number of files currently uploaded
        $randomName = substr(base_convert(bin2hex(random_bytes($length)), 16, 36), 0, $length);

        // Make sure the filename isn't already being used
        $file = self::where('url_path', "/{$randomName}.{$extension}")->first();

        if(is_null($file)) {
            // If no such file already exists, then return the generated name
            return "{$randomName}.{$extension}";
        } else {
            // Otherwise call this function again and retry
            return self::uniqueName($extension);
        }
    }
}
