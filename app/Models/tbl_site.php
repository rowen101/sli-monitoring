<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_site extends Model
{
    use HasFactory;

    protected $table = 'tbl_sites';

    protected $fillable = ['id','site_name','is_active'];
}
