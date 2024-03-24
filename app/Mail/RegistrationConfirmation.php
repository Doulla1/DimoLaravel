<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $nom;
    public $prenom;

    /**
     * Create a new message instance.
     */
    public function __construct( User $user)
    {
        $this->nom = $user->lastname;
        $this->prenom = $user->firstname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Confirmation de votre inscription')
            ->view('emails.registration-confirmation');
    }
}
