<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',

        // Relaciones
        'user_count_id',

        // Uditoria
        'user_id',
        'updated_by',
    ];


    public function user()
    {
        return $this->hasOne(User::class,'user_count_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }


}
