<?php

// database/seeders/InvoiceSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceDetail;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        Invoice::factory(15)->create()->each(function ($invoice) {
            InvoiceDetail::factory(rand(1, 5))->create([
                'invoice_id' => $invoice->id,
            ]);
        });
    }
}
