<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(), // 仮定しているProductモデルのファクトリーを利用
            // 'created_at' と 'updated_at' はEloquentが自動的に処理するので、ここに追加する必要はありません。
        ];
    }
}