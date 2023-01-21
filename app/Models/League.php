<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
  protected $fillable = [
    'owner',
    'competition',
    'name',
    'is_public',
  ];

  public function Owner()
  {
    return $this->belongsTo(User::class, 'owner');
  }

  public function Competition()
  {
    return $this->belongsTo(Competition::class, 'competition');
  }

  public function participants()
  {
    return $this->hasMany(Participant::class, 'league');
  }

  public function join_requests()
  {
    return $this->hasMany(JoinRequest::class, 'league');
  }
}
