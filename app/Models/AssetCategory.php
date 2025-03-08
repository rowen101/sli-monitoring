<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'categorycode','description','status','created_by','updated_by'];

    public function items()
    {
        return $this->hasMany(AssetItem::class);
    }
}
