<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code', 'type', 'supplier_id', 'transaction_date',
        'total_amount', 'deposit', 'final_amount', 'notes',
        'payment_method', 'payment_notes', 'paid_at', 'is_verified', 'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'paid_at' => 'datetime',
        'is_verified' => 'boolean',
        'total_amount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'final_amount' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Untuk relasi ke partner (alias supplier)
    public function partner()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabel()
    {
        return $this->type === 'purchase' ? 'PEMBELIAN' : 'PENJUALAN';
    }

    public function getPartnerLabel()
    {
        return $this->type === 'purchase' ? 'Supplier' : 'Pembeli';
    }

    public function getPaymentMethodLabel()
    {
        $labels = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'credit' => 'Kredit'
        ];
        return $labels[$this->payment_method] ?? '-';
    }

    public function hasReturns()
    {
        return $this->returns()->where('status', 'approved')->exists();
    }

    public function isVerified()
    {
        return $this->is_verified;
    }

    // Generate kode transaksi otomatis
    public static function generateCode($type)
    {
        $prefix = $type === 'purchase' ? 'PB' : 'SL';
        $date = now()->format('Ymd');
        $last = self::where('type', $type)
            ->whereDate('created_at', now())
            ->latest()
            ->first();

        $number = $last ? intval(substr($last->transaction_code, -4)) + 1 : 1;

        return 'MPC-' . $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
