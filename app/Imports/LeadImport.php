<?php

namespace App\Imports;

use App\leads;
use Maatwebsite\Excel\Concerns\ToModel;


class LeadImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new leads([
            //
            'source' => $row[0],
            'assignedto' => $row[1],
            'branch' => $row[2],
            'studentname' => $row[3],
            'address' => $row[4],
            'email' => $row[5],
            'phone' => $row[6],
            'course' => $row[7],
            'lvalue' => $row[8],
            'city' => $row[9],
            'state' => $row[10],
            'zipcode' => $row[11],

        ]);


    }
}
