<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'space_id',
        'user_id',
        'category_id',
        'description',
        'amount',
        'expense_date',
        'receipt',
        'notes',
        'status',
        'split_type',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function expenseParticipants()
    {
        return $this->hasMany(ExpenseParticipant::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
