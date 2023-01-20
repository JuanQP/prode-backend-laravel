<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    protected $fillable = [
        'competition',
        'team_a',
        'team_b',
        'datetime',
        'stadium',
        'description',
    ];

    public function predictions()
    {
        return $this->hasMany(Prediction::class, 'match');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition');
    }

    public function team_a()
    {
        return $this->belongsTo(Team::class, 'team_a');
    }

    public function team_b()
    {
        return $this->belongsTo(Team::class, 'team_b');
    }

    public function mark_as_finished(string $team_a_score, string $team_b_score)
    {
        if($this->status == 'Finalizado') {
            return;
        }
        DB::transaction(function() use($team_a_score, $team_b_score) {
            $this->team_a_score = $team_a_score;
            $this->team_b_score = $team_b_score;
            $this->status = 'Finalizado';
            $this->save();

            // Every single prediction must be updated
            foreach($this->predictions as $prediction) {
                $prediction->update_score($this);
            }
        });
    }
}
