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

final class IssueResolvedMail extends AbstractMail implements MailInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ConfigService   $configService
    ) {
    }

    public function send(...$context): void
    {
        [$username, $userEmail, $ticketNo, $issueTitle] = $context;

        $email = new TemplatedEmail()
            ->from(
                new Address(
                    $this->configService->getParameter('no_reply'),
                    $this->configService->getParameter('app_name')
                )
            )
            ->to(new address($userEmail, $username))
            ->subject(sprintf('Khalilleo-Helpdesk: Issue T-%s is Currently Being Resolved', $ticketNo))
            ->htmlTemplate('mails/dashboard/issue_resolved.html.twig')
            ->textTemplate('mails/dashboard/issue_resolved.txt.twig')
            ->context([
                'username' => $username,
                'issueTitle' => $issueTitle,
            ]);

        Mailer:: catch(sprintf('Issue resolved email has been set to [%s]', $userEmail));

        $this->mailer->send($email);
    }
}
