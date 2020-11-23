<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Faker $faker
     * @return void
     */
    public function run(Faker $faker)
    {
        $user = User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => bcrypt('123')
        ]);


        DB::table('accounts')->insert([
            'number' => $faker->bankAccountNumber,
            'balance' => $faker->numberBetween(0, 10000),
            'currency' => $faker->currencyCode,
            'user_id' => $user->id
        ]);

        $user2 = User::create([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => bcrypt('123')
        ]);


        DB::table('accounts')->insert([
            'number' => $faker->bankAccountNumber,
            'balance' => $faker->numberBetween(0, 10000),
            'currency' => $faker->currencyCode,
            'user_id' => $user2->id
        ]);
    }
}
