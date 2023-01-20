<?php

namespace App\Imports;

use App\Http\Requests\MatchRequest;
use App\Models\Game;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MatchesImport implements ToModel, WithHeadingRow
{
    private $competitionId;

    function __construct($id)
    {
        $this->competitionId = $id;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            "competition" => $this->competitionId,
            "team_a" => $row["team_a"],
            "team_b" => $row["team_b"],
            "datetime" => $row["datetime"],
            "stadium" => $row["stadium"],
            "description" => $row["description"],
        ];
        $rules = array_intersect_key(MatchRequest::$rules, $data);
        $validator = Validator::make($data, $rules);

        return new Game($validator->validated());
    }
}
