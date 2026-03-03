<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Inversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'monto',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($inversion) {
            $inversion->project->increment('inversion_actual', $inversion->monto);
        });
    }
}