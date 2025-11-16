<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id', 'item_id', 'quantity', 'price', 'subtotal', 'quantity_returned'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity_returned' => 'decimal:2'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnItem::class);
    }

    // Qty efektif setelah retur
    public function getEffectiveQuantity()
    {
        return $this->quantity - $this->quantity_returned;
    }

    // Subtotal efektif setelah retur
    public function getEffectiveSubtotal()
    {
        return $this->getEffectiveQuantity() * $this->price;
    }

    // Cek apakah barang diretur penuh
    public function isFullyReturned()
    {
        return $this->quantity_returned >= $this->quantity;
    }
}
