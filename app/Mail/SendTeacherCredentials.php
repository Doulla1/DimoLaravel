<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendTeacherCredentials extends Mailable
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
            ->view('emails.SendCredentialsToTeacher');
    }
}
