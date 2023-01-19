<?php

namespace App\Imports;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeamsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            "name" => $row["name"],
            "short_name" => $row["short_name"],
        ];
        $rules = array_intersect_key(TeamRequest::$rules, $data);
        $validator = Validator::make($data, $rules);

        return new Team($validator->validated());
    }
}
