<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_at',
        'end_at',
        'all_day',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'all_day' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')->withPivot('viewed_at')->withTimestamps();
    }

    public function isParticipant(int $userId): bool
    {
        return $this->user_id === $userId || $this->participants()->where('user_id', $userId)->exists();
    }
}
