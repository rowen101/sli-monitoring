<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSites extends Model
{
    protected $table = 'user_sites';

    protected $fillable = ['id','user_id','site_id'];
}
