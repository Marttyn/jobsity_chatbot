<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Auth;

class LoginConversation extends Conversation
{
    /** @var array $credentials */
    protected $credentials;

    /**
     * First question. Ask whats the user's email.
     */
    public function askEmail()
    {
        $this->ask('What is your email?', function (Answer $answer) {
            $this->credentials['email'] = $answer->getText();

            $this->askPassword();
        });
    }

    /**
     * Ask whats the user's password.
     */
    public function askPassword()
    {
        $this->ask('One more thing - what is your password?', function (Answer $answer) {
            $this->credentials['password'] = $answer->getText();

            if (Auth::attempt($this->credentials)) {
                $this->say("Congrats, now you are logged in!!");
                $_SESSION['user_id'] = $this->getBot()->getUser()->getId();
                $_SESSION['user_email'] = $this->credentials['email'];
            } else {
                $this->say("Sorry, I couldn't find your user.");
            }

        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askEmail();
    }
}
