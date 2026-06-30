<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'space_type',
        'name',
        'icon',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeForSpaceType($query, ?string $spaceType)
    {
        return $query->where(function ($q) use ($spaceType) {
            $q->whereNull('space_type');
            if ($spaceType) {
                $q->orWhere('space_type', $spaceType);
            }
        });
    }
}
