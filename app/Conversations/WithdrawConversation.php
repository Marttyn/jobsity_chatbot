<?php

namespace App\Conversations;

use App\Accounts;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class WithdrawConversation extends Conversation
{
    /** @var string $currency */
    protected $currency;
    /** @var double $ratio */
    protected $rate;
    /** @var double $amount */
    protected $amount;
    /** @var User $user */
    protected $user;

    /**
     * DepositConversation constructor.
     */
    public function __construct()
    {
        $this->user = User::where('email', $_SESSION['user_email'])->first();
    }

    /**
     * First question. Asks in what currency the user want the money.
     */
    public function askCurrency()
    {
        $this->ask('What currency do you want?', function (Answer $answer) {
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
                $this->rate = $parsed_json['rates'][$this->user->account->currency];
                $this->askAmount();
            }
        });
    }

    /**
     * Asks how much money in the choosen currency he wants to withdraw
     */
    private function askAmount()
    {
        $this->ask('How much do you want to withdraw?', function (Answer $answer) {
            $this->amount = $answer->getText();

            if (!is_numeric($this->amount)){
                $this->repeat('Sorry this is not valid number.');
            } else {
                $this->amount *= $this->rate;

                /** @var Accounts $account */
                $account = $this->user->account;
                $account->balance -= $this->amount;

                if ($account->balance < 0) {
                    $this->say("Sorry you don't have balance to withdraw this amount.");
                } else {
                    $account->save();
                    $this->say("{$this->user->name} your new balance is {$account->balance} {$account->currency}");
                }

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
