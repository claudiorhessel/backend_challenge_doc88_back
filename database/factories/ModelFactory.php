<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\{User, Models\Clients};
use Faker\Generator as Faker;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->define(Client::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => preg_replace('/@example\..*/', '@domain.com', $faker->unique()->safeEmail),
        'phone' => $faker->randomNumber(9),
        'birth_date' => $faker->date,
        'address' => $faker->email,
        'complement' => $faker->text(10),
        'neighborhood' => $faker->text(10),
        'cep' => $faker->randomNumber(9),
    ];
});
