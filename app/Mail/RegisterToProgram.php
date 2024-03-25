<?php

namespace App\Mail;

use App\Models\Program;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterToProgram extends Mailable
{
    use Queueable, SerializesModels;

    public $program;
    public $user;


    /**
     * Create a new message instance.
     */
    public function __construct( User $user, Program $program)
    {
        $this->program = $program;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Inscription à :' . $this->program->name)
            ->view('emails.register-to-program');
    }
}
