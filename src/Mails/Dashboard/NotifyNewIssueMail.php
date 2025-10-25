<?php

declare(strict_types=1);

namespace App\Mails\Dashboard;

use App\Mails\AbstractMail;
use App\Mails\MailInterface;
use App\Service\Core\ConfigService;
use App\Service\Dev\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class NotifyNewIssueMail extends AbstractMail implements MailInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ConfigService   $configService
    ) {
    }

    public function send(...$context): void
    {
        $webmasterName = $this->configService->getParameter('webmasterName');

        $webmasterEmail = $this->configService->getParameter('webmasterEmail');

        $email = new TemplatedEmail()
            ->from(
                new Address(
                    $this->configService->getParameter('no_reply'),
                    $this->configService->getParameter('app_name')
                )
            )
            ->to(new address($webmasterEmail, $webmasterName))
            ->subject('Khalilleo-Helpdesk: A New Issue Has Been Created')
            ->htmlTemplate('mails/dashboard/notify_new_issue.html.twig')
            ->textTemplate('mails/dashboard/notify_new_issue.txt.twig')
            ->context([
                'username' => $webmasterName
            ]);

        Mailer:: catch(sprintf('Webmaster has been informed about new issue. Email was sent to [%s]', $webmasterEmail));

        $this->mailer->send($email);
    }
}
