<?php
session_start();

use App\Conversations\BalanceConversation;
use App\Http\Controllers\BotManController;
use App\User;

$botman = resolve('botman');

$botman->fallback(function($bot) {
    $bot->reply('Sorry, I did not understand these commands.');
});

$botman->hears('test', function ($bot) {
    $urlConvert = "https://api.exchangeratesapi.io/latest?base=BRL";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $urlConvert);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $json_string = curl_exec($ch);
    $parsed_json = json_decode($json_string, true);

    $rates = $parsed_json['rates'];
    $currency = 1000 * $rates['USD'];

    $currency = number_format($currency, 2);

    $bot->reply("You balance {$currency} USD");
});

$botman->hears('Login', BotManController::class . '@startLoginConversation');

$botman->hears('Logout', function ($bot) {
    $_SESSION['user_id'] = null;
    $_SESSION['user_email'] = null;

    $bot->reply("Ok, see you later.");
});

$botman->hears('(Sign in|Signin)', BotManController::class . '@startSignInConversation');

$botman->hears('Name', BotManController::class . '@startNameConversation');

$botman->hears('Balance', BotManController::class . '@startBalanceConversation');

$botman->hears('Deposit', BotManController::class . '@startDepositConversation');

$botman->hears('Withdraw', BotManController::class . '@startWithdrawConversation');

$botman->hears('Change currency', BotManController::class . '@startChangeCurrencyConversation');

$botman->hears('Start conversation', BotManController::class . '@startConversation');