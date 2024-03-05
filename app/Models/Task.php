<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\TaskType;
class Task extends Model
{
    use HasFactory;
    protected $table = 'tbl_dailytask';
    protected $primaryKey = 'dailytask_id';
    protected $fillable = [
        'dailytask_id',
        'user_id',
        'site',
        'taskdate',
        'project',
        'plandate',
        'planenddate',
        'startdate',
        'enddate',
        'tasktype',
        'status',
        'attachment',
        'PWS',
        'remarks',
        'immediate_hid',
        'status_task',
        'created_at'

    ];

    public function taskLists()
    {
        return $this->hasMany(ListTask::class, 'dailytask_id');
    }


    protected $casts = [
        'taskdate' => 'datetime',
        'created_at' => 'datetime',
        'plandate' => 'datetime',
        'planenddate' => 'datetime',
        'startdate' => 'datetime',
        'enddate' => 'datetime',
        'tasktype' => TaskType::class,
    ];

}
