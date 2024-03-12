<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenus extends Model
{
    protected $table = "usermenus";

    protected $fillable = [
        'user_id',
        'menu_id',
        'flag',
        'caninsert',
        'candelete',
        'canedit',
        'canview',
        'favorite',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
