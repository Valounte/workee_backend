<?php

namespace App\Client\Emails\User;

use Symfony\Component\Mime\Email;
use App\Core\Components\User\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class RegistrationEmailGenerator
{
    public static function generate(User $user, string $token): TemplatedEmail
    {
        $emailTemplate = 'User/registrationEmail.html.twig';

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
