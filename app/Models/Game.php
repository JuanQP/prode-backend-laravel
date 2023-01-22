<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function Competition()
    {
        return $this->belongsTo(Competition::class, 'competition');
    }

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b');
    }

    public function match_started_or_finished()
    {
        $is_match_finished = $this->status == 'Finalizado';
        $is_match_started = Carbon::now() > $this->datetime;

        return $is_match_finished || $is_match_started;
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
