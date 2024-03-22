<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    protected $table = 'pallets';

    protected $fillable = ['id','site_id','user_id','date','allocatedpalletspace','spaceuteltotal','caseperpallet'];
}
