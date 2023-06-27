<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookieRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function getProfilesAttribute()
    {
        $profiles = $this->attributes['profiles'];

        if (is_string($profiles)) {
            $profiles = json_decode($profiles);
        }

        return $profiles;
    }

    public function getEmailAttribute()
    {
        return $this->attributes['email'] ?? "Not Found";
    }
}
