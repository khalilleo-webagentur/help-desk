<?php

declare(strict_types=1);

namespace App\Mails\Account;

use App\Mails\AbstractMail;
use App\Mails\MailInterface;
use App\Service\Core\ConfigService;
use App\Service\Dev\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class AccountConfirmationMail extends AbstractMail implements MailInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ConfigService   $configService
    ) {
    }

    public function send(...$context): void
    {
        [$username, $userEmail, $token] = $context;

        $email = new TemplatedEmail()
            ->from(
                new Address(
                    $this->configService->getParameter('no_reply'),
                    $this->configService->getParameter('app_name')
                )
            )
            ->to(new address($userEmail, $username))
            ->subject('Khalilleo-Helpdesk: Please Verify Your Email Address')
            ->htmlTemplate('mails/account/confirmation_email.html.twig')
            ->textTemplate('mails/account/confirmation_email.txt.twig')
            ->context([
                'username' => $username,
                'token' => $token,
            ]);

        Mailer:: catch('/verify/email/' . $token);

        $this->mailer->send($email);
    }
}
