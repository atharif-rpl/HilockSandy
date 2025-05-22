<?php

// app/Models/InvoiceDetail.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'coil_number',
        'width',
        'length',
        'thickness',
        'weight',
        'price',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}