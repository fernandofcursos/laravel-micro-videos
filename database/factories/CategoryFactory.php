<?php

namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'description' => rand(1, 10) % 2 == 0 ? $faker->sentence() : null
    ];
});

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
// class CategoryFactory extends Factory
// {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // public function definition()
    // {
    //     return [
    //         'name' => $faker->colorName,
    //         'description' => rand(1, 10) % 2 == 0 ? $faker->sentence() : null
    //     ];
    // }

// }
