<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the clients associated with the user.
     */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the projects associated with the user.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('is_responsible')
            ->withTimestamps();
    }
    /**
     * Get the subprojects associated with the user.
     */
    public function subprojects()
    {
        return $this->belongsToMany(SubProject::class, 'subproject_user')
            ->withPivot('is_responsible')
            ->withTimestamps();
    }

    /**
     * Get the tickets associated with the user.
     */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_user')
            ->withPivot('is_responsible')
            ->withTimestamps();
    }
    /**
     * Get the tasks associated with the user.
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')
            ->withPivot('is_responsible')
            ->withTimestamps();
    }

    public function usersProjects()
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('is_responsible')
            ->withTimestamps();
    }

    public function usersSubProjects()
    {
        return $this->belongsToMany(SubProject::class)
            ->withPivot('is_responsible')
            ->withTimestamps();
    }

    public function usersTickets()
    {
        return $this->belongsToMany(Ticket::class)
            ->withPivot('is_responsible')
            ->withTimestamps();
    }
    public function usersTasks()
    {
        return $this->belongsToMany(Task::class)
            ->withPivot('is_responsible')
            ->withTimestamps();
    }

    // Auditorias

    // Clientes

    public function createdClient()
    {
        return $this->hasMany(Client::class, 'user_id');
    }
    public function updatedClient()
    {
        return $this->hasMany(Client::class, 'updated_by');
    }

    // Proyectos
    public function createdProject()
    {
        return $this->hasMany(Project::class, 'user_id');
    }
    public function updatedProject()
    {
        return $this->hasMany(Project::class, 'updated_by');
    }
    // Subproyectos
    public function createdSubProject()
    {
        return $this->hasMany(SubProject::class, 'user_id');
    }
    public function updatedSubProject()
    {
        return $this->hasMany(SubProject::class, 'updated_by');
    }

    // Tickets
    public function createdTicket()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }
    public function updatedTicket()
    {
        return $this->hasMany(Ticket::class, 'updated_by');
    }
    // Tareas
    public function createdTask()
    {
        return $this->hasMany(Task::class, 'user_id');
    }
    public function updatedTask()
    {
        return $this->hasMany(Task::class, 'updated_by');
    }
    public function createdComment()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }
    public function updatedComment()
    {
        return $this->hasMany(Comment::class, 'updated_by');
    }
}
