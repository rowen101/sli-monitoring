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
        'item_no',
        'particulars',
        'description',
        'quantity',
        'uom',
        'unit_price',
        'total_amount',
        'created_by',
        'updated_by',
      
    ];

   
}
