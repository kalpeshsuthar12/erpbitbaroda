<?php

namespace App\Imports;

use App\Questions;
use Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class QuestionsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $userId  = Auth::user()->id;

        return new Questions([
            'qcourseid' => $row[0],
            'qlectures' => $row[1],
            'qquestions' => $row[2],
            'qusersids' => $userId,
            'aoptions' => $row[3],
            'boptions' => $row[4],
            'coptions' => $row[5],
            'doptions' => $row[6],
            'correctanswers' => $row[7],
            
        ]);
    }
}
