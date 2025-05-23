<?php 




// src/Service/MailerService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    // Injection du service MailerInterface dans le constructeur
    public function __construct( MailerInterface $mailer)
    {   
        $this->mailer = $mailer;
    }

    public function sendEmail(
        $to = 'skanderturki4@gmail.com',
        $content ='<p>See Twig integration for better HTML integration!</p>',
        $subject = 'Time for Symfony Mailer!'
    ): void
    {
        // Création de l'email
        $email = (new Email())
            ->from('skanderturki7050@gmail.com')   // L'email de l'expéditeur
            ->to($to)      // L'email du destinataire
            //->cc('cc@example.com')      // Si vous avez des copies
            //->bcc('bcc@example.com')    // Si vous avez des copies cachées
            //->replyTo('fabien@example.com') // Répondre à une autre adresse
            //->priority(Email::PRIORITY_HIGH) // Si vous souhaitez définir une priorité
            ->subject($subject)  // Sujet de l'email
            ->text('Sending emails is fun again!') // Contenu en texte brut
            ->html($content); // Contenu HTML

        // Envoi de l'email
        $this->mailer->send($email);
    }
}
