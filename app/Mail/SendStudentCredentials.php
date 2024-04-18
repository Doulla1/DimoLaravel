<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendStudentCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $lastname;
    public $firstname;
    public $email;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($firstname, $lastname, $email, $password)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Vos identifiants de connexion à DimoVR')
            ->view('emails.SendCredentialsToStudent');
    }
}
