<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(OrderItem::class, function (Faker $faker) {
    return [
        'order_id' =>  Order::all(['id'])->random()->id,
        'product_id' =>  Product::all(['id'])->random()->id,
        'quantity' => $faker->randomNumber(3),
        'total_value' => $faker->randomFloat(0, 3, 3),
    ];
});
