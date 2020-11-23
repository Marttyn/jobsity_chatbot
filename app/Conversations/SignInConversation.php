<?php

namespace App\Conversations;

use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class SignInConversation extends Conversation
{
    /** @var User $user */
    protected $user;
    /** @var string $account_number */
    protected $account_number;
    /** @var double $account_balance */
    protected $account_balance;
    /** @var string $account_currency */
    protected $account_currency;

    /**
     * SignInConversation constructor.
     */
    public function __construct()
    {
        $this->user = new User;
    }


    /**
     *  First question. Asks whats the user's name.
     */
    public function askName()
    {
        $this->ask('What is your name?', function (Answer $answer) {
            $this->user->name = $answer->getText();

            $this->askEmail();
        });
    }

    /**
     * Asks whats the user's email.
     */
    public function askEmail()
    {
        $this->ask('What is your email?', function (Answer $answer) {
            $this->user->email = $answer->getText();

            $user = User::where('email', $this->user->email)->first();

            if (isset($user)) {
                $this->repeat('Sorry this email is already in use. Please enter a valid email.');
            } else {
                $this->askPassword();
            }
        });
    }

    /**
     * Asks whats the user's password.
     */
    public function askPassword()
    {
        $this->ask('What is your password?', function (Answer $answer) {
            $this->user->password = bcrypt($answer->getText());

            $this->askBalance();
        });
    }

    /**
     * Asks how much the user will deposit in the new account.
     */
    public function askBalance()
    {
        $this->ask('Now about your account, how much you want to deposit?', function (Answer $answer) {
            $this->account_balance = $answer->getText();

            $this->askCurrency();
        });
    }

    /**
     * Asks whats the default currency of the account.
     */
    public function askCurrency()
    {
        $this->ask('What is you main currency?', function (Answer $answer) {
            $this->account_currency = $answer->getText();

            $urlConvert = "https://api.exchangeratesapi.io/latest?base={$this->account_currency}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlConvert);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $json_string = curl_exec($ch);
            $parsed_json = json_decode($json_string, true);

            if (isset($parsed_json['error'])) {
                $this->say($parsed_json['error']);
                $this->repeat();
            } else {
                $this->say('Great - that is all we need, ' . $this->user->name);

                $this->account_number = sprintf("%06d", mt_rand(1, 999999));

                $this->user->save();
                $this->user->account()->create([
                    'number' => $this->account_number,
                    'balance' => $this->account_balance,
                    'currency' => $this->account_currency
                ]);

                $this->say($this->user->name . ' your account number is ' . $this->account_number);
            }
        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askName();
    }
}
