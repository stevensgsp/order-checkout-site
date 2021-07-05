<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::insert([
            [
                'name' => 'Single Product',
                'description' => 'This is the only product you can buy on this site :v',
                'image_url' => 'https://picsum.photos/350/150?hash=' . Str::random(10),
                'price' => 2499, // this means: 24.99
                'currency' => 'USD'
            ]
        ]);
    }
}
