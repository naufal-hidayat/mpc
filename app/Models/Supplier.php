<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'address', 'phone'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Format nomor WA (hapus karakter selain angka dan tambah 62)
    public function getWhatsappNumberAttribute()
    {
        if (!$this->phone) return null;

        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
