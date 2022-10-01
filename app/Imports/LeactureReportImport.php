<?php

namespace App\Imports;

use App\lecturereport;
use Maatwebsite\Excel\Concerns\ToModel;

class LeactureReportImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new lecturereport([
            'subcourse'  => $row[0],
            'lecture'    => $row[1], 
            'details'    => $row[2], 
            
        ]);
    }
}
