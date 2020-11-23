<?php

namespace App\Http\Controllers;

use App\Conversations\ChangeCurrencyConversation;
use App\Conversations\DepositConversation;
use App\Conversations\LoginConversation;
use App\Conversations\SignInConversation;
use App\Conversations\WithdrawConversation;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\BalanceConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Start Sign In Conversation
     * @param BotMan $bot
     */
    public function startSignInConversation(BotMan $bot)
    {
        $bot->startConversation(new SignInConversation());
    }

    /**
     * Start Login Conversation
     * @param BotMan $bot
     */
    public function startLoginConversation(BotMan $bot)
    {
        $bot->startConversation(new LoginConversation());
    }

    /**
     * Start Balance Conversation
     * @param BotMan $bot
     */
    public function startBalanceConversation(BotMan $bot)
    {
        if (isset($_SESSION['user_email'])) {
            $bot->startConversation(new BalanceConversation());
        } else {
            $this->askToLogin($bot);
        }
    }

    /**
     * Start Deposit Conversation
     * @param BotMan $bot
     */
    public function startDepositConversation(BotMan $bot)
    {
        if (isset($_SESSION['user_email'])) {
            $bot->startConversation(new DepositConversation());
        } else {
            $this->askToLogin($bot);

        }
    }

    /**
     * Start Withdraw Conversation
     * @param BotMan $bot
     */
    public function startWithdrawConversation(BotMan $bot)
    {
        if (isset($_SESSION['user_email'])) {
            $bot->startConversation(new WithdrawConversation());
        } else {
            $this->askToLogin($bot);

        }
    }

    /**
     * Start ChangeCurrency Conversation
     * @param BotMan $bot
     */
    public function startChangeCurrencyConversation(BotMan $bot)
    {
        if (isset($_SESSION['user_email'])) {
            $bot->startConversation(new ChangeCurrencyConversation());
        } else {
            $this->askToLogin($bot);

        }
    }

    /**
     * Asks user if he wants to login
     * @param BotMan $bot
     */
    private function askToLogin(BotMan $bot) {
        $bot->ask('You are not logged in, do you want to Login?', [
            [
                'pattern' => 'yes|yep',
                'callback' => function () {
                    $this->say('Okay - we\'ll keep going');
                    $this->bot->startConversation(new LoginConversation());
                }
            ],
            [
                'pattern' => 'nah|no|nope',
                'callback' => function () {
                    $this->say('Ok, see you later.');
                }
            ]
        ]);
    }
}
