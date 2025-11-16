<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'unit', 'price', 'price_updated_at'];

    protected $casts = [
        'price_updated_at' => 'date',
        'price' => 'decimal:2'
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class)->orderBy('changed_at', 'desc');
    }

    // Simpan histori harga saat harga berubah
    public static function boot()
    {
        parent::boot();

        static::updating(function ($item) {
            if ($item->isDirty('price')) {
                PriceHistory::create([
                    'item_id' => $item->id,
                    'old_price' => $item->getOriginal('price'),
                    'new_price' => $item->price,
                    'changed_at' => now(),
                    'changed_by' => auth()->id()
                ]);
                $item->price_updated_at = now();
            }
        });
    }
}
