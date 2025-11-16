<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'item_id', 'old_price', 'new_price', 'changed_at', 'changed_by'
    ];

    protected $casts = [
        'changed_at' => 'date',
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
