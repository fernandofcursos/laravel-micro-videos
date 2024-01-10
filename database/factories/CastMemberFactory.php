<?php

namespace Database\Factories;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CastMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            //
            'name' => fake()->lastName(),
            'type' => array_rand([CastMember::TYPE_ACTOR, CastMember::TYPE_ACTOR])
        ];
    }
}
// <?php

// /** @var \Illuminate\Database\Eloquent\Factory $factory */

// use App\Models\CastMember;
// use Faker\Generator as Faker;

// $factory->define(CastMember::class, function (Faker $faker) {
//     return [
//         'name' => $faker->lastName,
//         'type' => array_rand([CastMember::TYPE_DIRECTOR, CastMember::TYPE_DIRECTOR])
//     ];
// });