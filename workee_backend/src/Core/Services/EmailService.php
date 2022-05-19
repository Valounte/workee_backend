<?php

namespace App\Core\Services;

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function sendRegistrationEmail(): void
    {
        $emailTemplate = 'emails/registrationEmail.html.twig';

        $email = (new TemplatedEmail())
        ->from('workee@gmail.com')
        ->to('valentin.lyon@epitech.eu')
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Here is your registration email !')
        ->context(['name' => 'Valentin', 'hash' => 'biboubibou'])
        ->htmlTemplate($emailTemplate);

        $this->mailer->send($email);
    }
}
