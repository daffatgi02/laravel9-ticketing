<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to_user_id',
        'assigned_to_department',
        'assigned_at',
        'resolved_at',
        'closed_at'
    ];

    protected $dates = [
        'assigned_at',
        'resolved_at',
        'closed_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    // Helper for generating ticket numbers
    public static function generateTicketNumber()
    {
        $lastTicket = self::orderBy('id', 'desc')->first();
        $number = $lastTicket ? intval(substr($lastTicket->ticket_number, 4)) + 1 : 1;
        return 'TKT-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
