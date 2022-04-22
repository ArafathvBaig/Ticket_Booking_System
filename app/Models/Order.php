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

    /**
     * Function to get the Orders
     * Passing the user as a parameter
     * 
     * @return array
     */
    public static function getOrders($user)
    {
        $orders = Order::leftJoin('tickets', 'tickets.id', '=', 'orders.ticket_id')
        ->select('orders.id', 'orders.user_id', 'orders.ticket_id', 'orders.ticket_count', 'tickets.title', 'tickets.cost')
        ->where('orders.user_id', $user->id)->get();
        return $orders;
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function tickets()
    {
        return $this->belongsTo(Ticket::class);
    }
}
