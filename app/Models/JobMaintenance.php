<?php

namespace App\Models;
use App\Enums\JobOrderRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobMaintenance extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'job_maintenances';

    protected $primaryKey = 'id';
    protected $fillable =[
        'id',
        'job_order_number',
        'site_id',
        'end_user',
        'time_requested',
        'date_needed',
        'noted_by',
        'type_of_job',
        'problem_description',
        'findings_recommendations',
        'commitment_date',
        'status',
        'created_by',
        'updated_by',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',

    ];

    public function replacement_parts()
    {
        return $this->hasMany(JobReplacementPart::class, 'job_order_request_id', 'id');
    }

}
