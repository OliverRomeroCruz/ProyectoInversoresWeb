<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Inversion;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'imagen_url',
        'video_url',
        'min_inversion',
        'max_inversion',
        'inversion_actual',
        'fecha_fin',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inversiones()
    {
        return $this->hasMany(Inversion::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


}

