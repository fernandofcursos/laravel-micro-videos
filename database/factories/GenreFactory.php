<?php

// namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
// class GenreFactory extends Factory
// {
//     /**
//      * Define the model's default state.
//      *
//      * @return array<string, mixed>
//      */
//     public function definition()
//     {
//         return [
//             //
//         ];
//     }
// }

use App\Models\Genre;
use Faker\Generator as Faker;

$factory->define(Genre::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
    ];
});