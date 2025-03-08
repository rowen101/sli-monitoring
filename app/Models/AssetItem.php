<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model
{
    use HasFactory;
    protected $fillable = ['name','itemcode', 'description', 'category_id','status','created_by','updated_by'];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }
}
