<?php

// database/factories/InvoiceFactory.php
namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $yearMonth = Carbon::now()->subMonths(rand(0,12))->format('ym');
        static $invoiceCounter = []; // Static counter per yearMonth
        if (!isset($invoiceCounter[$yearMonth])) {
            $invoiceCounter[$yearMonth] = 0;
        }
        $invoiceCounter[$yearMonth]++;
        $sequence = str_pad($invoiceCounter[$yearMonth], 4, '0', STR_PAD_LEFT);

        return [
            'invoice_number' => 'JSGI-INV-' . $yearMonth . '-' . $sequence,
            'customer_name' => $this->faker->company,
            'delivery_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'submit_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
