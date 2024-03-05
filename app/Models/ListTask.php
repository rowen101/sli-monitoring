<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListTask extends Model
{
    use HasFactory;
    protected $table = 'tbl_tasklist';
    protected $fillable = [
        'id',
        'dailytask_id',
        'task_name',
        'iscompleted'

    ];

}
