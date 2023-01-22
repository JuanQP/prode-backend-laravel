<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    private $POINTS_FOR_GUESSING_WINNER = 45;
    private $POINTS_FOR_GUESSING_GOALS = 15;

    protected $fillable = [
        'match',
        'participant',
        'team_a_score',
        'team_b_score',
    ];

    public function Participant()
    {
        return $this->belongsTo(Participant::class, 'participant');
    }

    public function Match()
    {
        return $this->belongsTo(Game::class, 'match');
    }

    public function is_owner(User $user)
    {
        return $this->Participant->User->id == $user->id;
    }

    public function update_score(Game $finished_match)
    {
        $total_points = 0;
        $guessed_tie = ($finished_match->team_a_score == $finished_match->team_b_score)
            && ($this->team_a_score == $this->team_b_score);
        $guessed_team_a_has_won = ($finished_match->team_a_score > $finished_match->team_b_score)
            && ($this->team_a_score > $this->team_b_score);
        $guessed_team_b_has_won = ($finished_match->team_a_score < $finished_match->team_b_score)
            && ($this->team_a_score < $this->team_b_score);
        $guessed_correctly = $guessed_tie || $guessed_team_a_has_won || $guessed_team_b_has_won;

        if($guessed_correctly) {
            $total_points += $this->POINTS_FOR_GUESSING_WINNER;
        }
        if($finished_match->team_a_score == $this->team_a_score) {
            $total_points += $this->POINTS_FOR_GUESSING_GOALS;
        }
        if($finished_match->team_b_score == $this->team_b_score) {
            $total_points += $this->POINTS_FOR_GUESSING_GOALS;
        }

        // Update participant
        $participant = $this->Participant;
        $participant->score += $total_points;
        $participant->save();
    }

    public function toString()
    {
        $team_a = $this->Match->teamA->short_name;
        $score_a = $this->team_a_score;
        $team_b = $this->Match->teamB->short_name;
        $score_b = $this->team_b_score;

        if($score_a == '' || $score_b == '') {
            return "{$team_a} - {$team_b}";
        }

        return "{$team_a} {$score_a} - {$team_b} {$score_b}";
    }
}
