<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];
    protected $table = 'menus';
    protected $primaryKey = 'menu_id';
    protected $fillable = ['menu_id', 'menu_code', 'menu_title', 'description','parent_id','menu_icon','menu_route','sort_order','is_active'];

    public function submenus()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function getMenu()
    {
        return static::where('parent_id', null)->with('submenus')->get();
    }

    public function userMenus()
    {
        return $this->hasMany(UserMenus::class, 'menu_id', 'id');
    }

    protected $casts = [
        'created_at' => 'datetime',
        'is_active' => 'boolean',
    ];


}
