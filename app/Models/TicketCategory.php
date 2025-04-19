<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'assigned_to',
        'active'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}
