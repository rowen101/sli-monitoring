<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrfForm extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'mrf_form';

    protected $primaryKey = 'id';
    protected $fillable =[
        'id',
        'mrf_order_number',
        'site_id',
        'date_requested',
        'date_needed',
        'purpose',
        'status',
        'prepared_by',
        'prepared_by_signature',
        'noted_by',
        'noted_by_signature',
        'finance_id',
        'finance_signature',
        'created_by',
        'updated_by',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',

    ];

    public function mrf_items_parts()
    {
        return $this->hasMany(MrfItems::class, 'mrf_form_id', 'id');
    }
}
