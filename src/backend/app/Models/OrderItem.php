<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];
    // OrderItem ini merujuk kepada Order induk
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    // OrderItem ini merujuk kepada Product yang dibeli
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
