<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',

        // Relaciones
        'client_id',
        // Auditoria
        'user_id',
        'updated_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subProjects()
    {
        return $this->hasMany(SubProject::class);
    }

    // Relación polimórfica: Un proyecto puede tener muchos tickets
    public function tickets()
    {
        return $this->morphMany(Ticket::class, 'designation');
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
            ->withTimestamps();;
    }
}
