<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sliassetmonitoring extends Model
{
    protected $table = 'sliassetmonitoring';

    protected $fillable = [
        'asset_name',
        'asset_type',
        'serial',
        'date_acquired',
        'man_supplier',
        'unit',
        'location',
        'paccountable',
        'locationchangetranfer',
        'cucodition',
        'maintenancenotes',
        'lastmaintenance',
        'nextmaintenance',
        'operationhours',
        'notes',
        'purchasecost',
        'insurancewarrantyinfo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',

    ];
}
