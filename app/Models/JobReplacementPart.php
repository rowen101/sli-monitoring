<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobReplacementPart extends Model
{
    use HasFactory;

    protected $table ="job_replacement_parts";
    protected $fillable =[
        'job_order_request_id',
        'description',
        'part_number',
        'quantity'
    ];
}
