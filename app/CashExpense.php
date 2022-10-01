<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashExpense extends Model
{
    protected $fillable=[
        'epayid','ebranchs','expoldamounts','expnsenewamounts','expensepaymode','exppaymendate','expensefor','expenseremarks','cusersids'
    ];
}
