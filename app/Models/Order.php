<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $fillable = [
        'user_id',
        'ticket_id',
        'ticket_count',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function tickets()
    {
        return $this->belongsTo(Ticket::class);
    }
}
