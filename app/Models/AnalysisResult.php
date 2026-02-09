<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_ad_id',
        'match_percentage',
        'llm_feedback',
        'status',
        'detailed_scores',
        'resume_path',
        'job_title',
    ];

    protected $casts = [
        'detailed_scores' => 'array',
        'llm_feedback' => 'array',
    ];

    public function jobAd()
    {
        return $this->belongsTo(JobAd::class);
    }
}
