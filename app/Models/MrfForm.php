<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrfForm extends Model
{
    use HasFactory;
    protected $table = 'mrf_form';

    protected $primaryKey = 'id';
    protected $fillable =[
        'id',
        'mrf_order_number',
        'site_id',
        'requisitioner',
        'date_requested',
        'date_needed',
        'purpose',
        'status',
        'prepared_by',
        'prepared_by_signature',
        'noted_by',
        'noted_by_signature',
        'created_by',
        'updated_by',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',

    ];

    public function mrf_items()
    {
        return $this->hasMany(MrfItems::class, 'mrf_form_id', 'id');
    }
}
