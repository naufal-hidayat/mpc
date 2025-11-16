<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'transaction_id', 'transaction_detail_id', 'quantity_returned',
        'reason', 'status', 'created_by'
    ];

    protected $casts = [
        'quantity_returned' => 'decimal:2'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
