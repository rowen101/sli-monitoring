<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = ['id','user_id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'];

    // protected $casts = [
    //     'data' => 'array',
    // ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
