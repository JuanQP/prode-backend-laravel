<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
  protected $fillable = [
    'user',
    'league',
    'score',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user');
  }

  public function league()
  {
    return $this->belongsTo(League::class, 'league');
  }
}
