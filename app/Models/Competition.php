<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = [
        'name',
    ];

    public function matches()
    {
        return $this->hasMany(Game::class, 'competition');
    }

    public function leagues()
    {
        return $this->hasMany(League::class, 'competition');
    }
}
