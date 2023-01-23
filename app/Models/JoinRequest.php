<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
  protected $fillable = [
    'league',
    'user',
    'accepted',
  ];

  public function League()
  {
    return $this->belongsTo(League::class, 'league');
  }

  public function User()
  {
    return $this->belongsTo(User::class, 'user');
  }
}
