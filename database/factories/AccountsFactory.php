<?php

use Faker\Generator as Faker;

$factory->define(App\Accounts::class, function (Faker $faker) {
    return [
        'number' => $faker->bankAccountNumber,
        'balance' => $faker->numberBetween(0, 10000),
        'currency' => $faker->currencyCode,
        'user_id' => factory(\App\User::class)->make(),
    ];
});
