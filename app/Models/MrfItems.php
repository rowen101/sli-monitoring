<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrfItems extends Model
{
    use HasFactory;
    protected $table = 'mrf_items';
    protected $fillable =[
        'id',
        'mrf_form_id',
        'particulars',
        'description',
        'quantity',
        'uom',
        'unit_price',
        'created_by',
        'updated_by',
      
    ];

   
}
