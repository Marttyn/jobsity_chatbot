<?php

namespace Http\Controllers;

use App\Accounts;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BotManControllerTest extends TestCase
{
//    public function testStartBalanceConversation()
//    {
//
//    }
//
//    public function testStartChangeCurrencyConversation()
//    {
//
//    }
//
//    public function testStartWithdrawConversation()
//    {
//
//    }
//
//    public function testStartDepositConversation()
//    {
//
//    }

    public function testStartSignInConversation()
    {
        $user = new User();
        $user->name = 'user';
        $user->email = $this->generateRandomString() . '@test.com';
        $user->password = bcrypt('123');
        $user->save();

        $account_number = sprintf("%06d", $user->id+1);

        $user->account()->create([
            'number' => sprintf("%06d", $user->id+1),
            'balance' => 100,
            'currency' => 'USD'
        ]);

        $name = 'test';
        $email = $this->generateRandomString() . '@test.com';
        $last_account_id = DB::table('accounts')->latest('id')->first()->id;
        $last_account_id = sprintf("%06d", $last_account_id+2);

        $this->bot
            ->receives('Sign In')
            ->assertReply('What is your name?')
            ->receives($name)
            ->assertReply('What is your email?')
            ->receives($email)
            ->assertReply('What is your password?')
            ->receives('password')
            ->assertReply('Now about your account, how much you want to deposit?')
            ->receives(100)
            ->assertReply('What is you main currency?')
            ->receives('USD')
            ->assertReplies([
                "Great - that is all we need, $name",
                "$name your account number is $last_account_id"
            ]);
    }

    public function testStartLoginConversation()
    {
        $user = DB::table('users')->first();

        $this->bot
            ->receives('Login')
            ->assertReply('What is your email?')
            ->receives($user->email)
            ->assertReply('One more thing - what is your password?')
            ->receives($user->password)
            ->assertReplyIn([
                'Congrats, now you are logged in!!',
                'Sorry, I couldn\'t find your user.'
            ]);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function tearDownAfterClass(): void
    {
        (new self())->setUp();
        Artisan::call('migrate:fresh');
    }
}
