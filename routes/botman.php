<?php
session_start();

use App\Conversations\BalanceConversation;
use App\Http\Controllers\BotManController;
use App\User;

$botman = resolve('botman');

$botman->fallback(function($bot) {
    $bot->reply('Sorry, I did not understand these commands.');
});

$botman->hears('Login', BotManController::class . '@startLoginConversation');

$botman->hears('Logout', function ($bot) {
    $_SESSION['user_id'] = null;
    $_SESSION['user_email'] = null;

    $bot->reply("Ok, see you later.");
});

$botman->hears('(Sign in|Signin)', BotManController::class . '@startSignInConversation');

$botman->hears('Balance', BotManController::class . '@startBalanceConversation');

$botman->hears('Deposit', BotManController::class . '@startDepositConversation');

$botman->hears('Withdraw', BotManController::class . '@startWithdrawConversation');

$botman->hears('Change currency', BotManController::class . '@startChangeCurrencyConversation');