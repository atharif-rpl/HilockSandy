<?php

// app/Models/Invoice.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'delivery_date',
        'submit_date',
        // 'total_amount' // Dihitung, jadi tidak perlu fillable jika tidak disimpan langsung
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'submit_date' => 'datetime',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    // Accessor untuk menghitung total amount
    public function getTotalAmountAttribute(): float
    {
        return $this->details->sum(function ($detail) {
            return $detail->price; // Asumsi price di detail adalah subtotal per item
        });
    }
}