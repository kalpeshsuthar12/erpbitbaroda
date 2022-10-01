<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryCalculations extends Model
{
    protected $fillable = ['datesofsalarys','user_details_id','usersworkinghrs','users_salarys','upurecollections','user_months','umsdays','workingdays','totalwrknghrs','ttlsphpl','ul','upl','flh','fld','uwrkinghrs','uwrkingsalary','uwrkingincentif','remarks'];
}
