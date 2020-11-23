<?php

namespace App\Conversations;

use App\Accounts;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class ChangeCurrencyConversation extends Conversation
{
    /** @var Accounts $account */
    protected $account;
    /** @var string $currency */
    protected $currency;

    /**
     * ChangeCurrencyConversation constructor.
     */
    public function __construct()
    {
        $this->account = User::where('email', $_SESSION['user_email'])->first()->account;
    }

    /**
     * First question. Ask the new default currency.
     */
    public function askCurrency()
    {
        $this->ask('Whats the new default currency?', function (Answer $answer) {
            $this->currency = $answer->getText();

            $urlConvert = "https://api.exchangeratesapi.io/latest?base={$this->currency}";
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
                $this->account->balance /= $parsed_json['rates'][$this->account->currency];
                $this->account->currency = $this->currency;

                $this->account->save();

                $this->say("Your account default currency was changed to {$this->currency}.");
                $this->say("Your account balance in the new currency is {$this->account->balance} {$this->currency}");
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
        $this->askCurrency();
    }
}
