<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'alias',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
