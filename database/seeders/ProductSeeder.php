<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $category = Category::updateOrCreate(
            ['slug' => Str::slug('Technology')],
            [
                'name'       => 'Technology',
                'image_path' => 'categories/03eCZddkwb7cea4QwLOk054vcTzRn1TPXguew0bn.jpg',
            ]
        );

        Product::updateOrCreate(
            ['slug' => Str::slug('HP 845 G8')],
            [
                'name'           => 'HP 845 G8',
                'quantity'       => 10,
                'sell_price'     => 42500,
                'purchase_price' => 37500,
                'image_path'     => 'products/RLR2wljHCaw9NXAEqU0vIOA0uV6NO0rbwCBtIO8v.jpg',
                'category_id'    => $category->id,
            ]
        );

        Product::updateOrCreate(
            ['slug' => Str::slug('iPhone 15 Pro Max')],
            [
                'name'           => 'iPhone 15 Pro Max',
                'quantity'       => 10,
                'sell_price'     => 220000,
                'purchase_price' => 180000,
                'image_path'     => 'products/QEXCGsbWWnSMpoc8gVfjmKr0ACFeYgwLPmIsnzJL.jpg',
                'category_id'    => $category->id,
            ]
        );
    }
}
