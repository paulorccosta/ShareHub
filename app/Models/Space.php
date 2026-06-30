<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'color',
        'icon',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'members')->withPivot('role', 'joined_at')->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'aberto');
    }

    public function isMember(int $userId): bool
    {
        return $this->owner_id === $userId || $this->members()->where('user_id', $userId)->exists();
    }
}
