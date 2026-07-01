<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
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
            'is_admin' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (static::count() === 0) {
                $user->is_admin = true;
            }
        });
    }

    public function ownedSpaces()
    {
        return $this->hasMany(Space::class, 'owner_id');
    }

    public function memberships()
    {
        return $this->hasMany(Member::class);
    }

    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'members')->withPivot('role', 'joined_at')->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function createdEvents()
    {
        return $this->hasMany(Event::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participants')->withPivot('viewed_at')->withTimestamps();
    }

    public function unreadEventsCount(): int
    {
        return $this->events()->wherePivotNull('viewed_at')->count();
    }
}
