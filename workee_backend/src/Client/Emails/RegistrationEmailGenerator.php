<?php

namespace App\Core\Services;

use App\Core\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class RegistrationEmailGenerator
{
    public static function generate(User $user, string $token): TemplatedEmail
    {
        $emailTemplate = 'emails/registrationEmail.html.twig';

        $email = (new TemplatedEmail())
        ->from('workee@gmail.com')
        ->to($user->getEmail())
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Here is your registration email !')
        ->context(['name' => $user->getFirstname(), 'token' => $token])
        ->htmlTemplate($emailTemplate);

        return $email;
    }
}
