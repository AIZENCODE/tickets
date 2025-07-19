<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subproject extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'project_id',
        // Auditoria
        'user_id',
        'updated_by',
    ];

    // Relación polimórfica: Un subproyecto puede tener muchos tickets
    public function tickets()
    {
        return $this->morphMany(Ticket::class, 'designation');
    }

    // Relación con el proyecto padre (opcional)
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_responsible')
            ->withTimestamps();
    }

}
