<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'status',
    ];
    // Setiap Order dimiliki oleh seorang User (Customer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Setiap Order mempunyai banyak OrderItems (senarai barang)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
