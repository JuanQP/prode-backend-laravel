<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
  protected $fillable = [
    'accepted',
  ];

  public function league()
  {
    return $this->belongsTo(League::class, 'league');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user');
  }
}
