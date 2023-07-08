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

    public function getProfilesAttribute($value)
    {
        return json_decode($value);
    }

    public function getEmailAttribute()
    {
        return $this->attributes['email'] ?? "Not Found";
    }

    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }

    public function scopeNotActive($query)
    {
        return $query->where("is_active", false);
    }

    public function scopeFreshAccounts($query)
    {
        return $query->whereNull("is_active");
    }
}
