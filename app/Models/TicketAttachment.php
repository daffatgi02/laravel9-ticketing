<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'message_id',
        'filename',
        'path',
        'mime_type',
        'size'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function message()
    {
        return $this->belongsTo(TicketMessage::class);
    }
}
