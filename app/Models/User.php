<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Add these to the existing User model
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'employee_id',
        'position',
        'phone',
        'active'
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to_user_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'hc';
    }

    public function isIT()
    {
        return $this->role === 'it';
    }

    public function isGA()
    {
        return $this->role === 'ga';
    }

    public function isSupport()
    {
        return $this->isIT() || $this->isGA();
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
