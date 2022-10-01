<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batchs_logs extends Model
{
    protected $fillable = ['batchesids','atransferfrom','atransferto','abatctime','adays','ajointos','aclassurls','astartdate','aenddate','abatchdurations','atransfdates'];
}
