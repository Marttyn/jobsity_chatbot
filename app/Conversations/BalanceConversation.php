<?php

namespace App\Conversations;

use App\User;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;

class BalanceConversation extends Conversation
{
    protected $currency;

    /**
     * First question. Asks whats the target currency
     *
     */
    public function askCurrency()
    {
        $error = null;

        $question = Question::create("In what currency do you want to see your account balance?");

        $this->ask('In what currency do you want see your balance?', function (Answer $answer) {
            $this->currency = $answer->getText();

            $user = User::where('email', $_SESSION['user_email'])->first();

            $url = "https://api.exchangeratesapi.io/latest";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $json_string = curl_exec($ch);
            $parsed_json = json_decode($json_string, true);

            if (isset($user) && $json_string !== false) {
                $currencies = $parsed_json['rates'];

                $array_keys = array_keys($currencies);

                if (in_array($this->currency, $array_keys)) {
                    if ($this->currency != $user->account->currency) {
                        $urlConvert = "https://api.exchangeratesapi.io/latest?base={$user->account->currency}";
                        curl_setopt($ch, CURLOPT_URL, $urlConvert);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        $json_string = curl_exec($ch);
                        $parsed_json = json_decode($json_string, true);

                        if (isset($parsed_json['error'])) {
                            $error = $parsed_json['error'];
                        } else {
                            $rates = $parsed_json['rates'];
                            $balance = $user->account->balance * $rates[$this->currency];
                            $balance = number_format($balance, 2);
                        }
                    } else {
                        $balance = $user->account->balance;
                    }

                    if (!isset($error)) {
                        $this->say("You balance {$balance} {$this->currency}");
                    }
                }
            } else {
                $this->say("Sorry an error happened, please try again.");
            }

            if (isset($error)) {
                $this->say($error);
            }

        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askCurrency();
    }
}
