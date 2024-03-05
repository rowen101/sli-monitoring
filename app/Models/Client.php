<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
    ];

    public function formattedcreatedby(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_by->format('Y-m-d'),
        );
    }

    // protected $appends = [
    //     'formatted_created_at',
    // ];

    // public function getFormattedCreatedAtAttribute()
    // {
    //     return $this->created_at->format(setting('date_format'));
    // }
}
