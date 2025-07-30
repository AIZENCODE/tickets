<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Ticket extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',

        'type',
        'description',
        'status',
        'priority',
        'start_date',
        'end_date',
        'closed_at',
        // Relaciones
        'client_id',
        // Relaciones polimórficas
        'designation_id',
        'designation_type',
        // Auditoria
        'user_id',
        'updated_by',
    ];

    // Relación polimórfica: Un ticket puede pertenecer a un proyecto o subproyecto
    public function designation()
    {
        return $this->morphTo();
    }

    // Relación con el cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }


    // Relación con el usuario creador
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

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
