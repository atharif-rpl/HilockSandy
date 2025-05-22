<?php

// database/factories/InvoiceDetailFactory.php
namespace Database\Factories;

use App\Models\InvoiceDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceDetailFactory extends Factory
{
    protected $model = InvoiceDetail::class;

    public function definition(): array
    {
        return [
            // invoice_id akan diisi saat seeder
            'coil_number' => 'COIL-' . strtoupper($this->faker->bothify('??####')),
            'width' => $this->faker->randomFloat(2, 50, 200),
            'length' => $this->faker->randomFloat(2, 100, 500),
            'thickness' => $this->faker->randomFloat(2, 0.5, 5),
            'weight' => $this->faker->randomFloat(2, 100, 1000),
            'price' => $this->faker->randomFloat(2, 500, 50000),
        ];
    }
}
