<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{


    protected $table = 'tbl_sites';

    protected $fillable = ['id','site_name','is_active'];

    public function userSite()
    {
        return $this->hasMany(UserSites::class, 'site_id', 'id');
    }
}
