<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_invoices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // Nomor invoice unik
            $table->string('customer_name', 100);
            $table->date('delivery_date');
            $table->timestamp('submit_date')->useCurrent(); // Otomatis diisi saat dibuat
            // $table->decimal('total_amount', 15, 2)->default(0); // Akan kita hitung dari detail
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};