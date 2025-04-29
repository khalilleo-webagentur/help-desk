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

final class NotifyIssueCommentMail extends AbstractMail implements MailInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ConfigService   $configService
    ) {
    }

    public function send(...$context): void
    {
        [$username, $userEmail, $ticketNo, $issueTitle, $comment] = $context;

        $email = (new TemplatedEmail())
            ->from(
                new Address(
                    $this->configService->getParameter('no_reply'),
                    $this->configService->getParameter('app_name')
                )
            )
            ->to(new address($userEmail, $username))
            ->subject(sprintf('Khalilleo-Helpdesk: new Comment on issue T-%s', $ticketNo))
            ->htmlTemplate('mails/dashboard/notify_issue_comment.html.twig')
            ->textTemplate('mails/dashboard/notify_issue_comment.txt.twig')
            ->context([
                'username' => $username,
                'ticketNo' => $ticketNo,
                'issueTitle' => $issueTitle,
                'comment' => $comment,
            ]);

        Mailer:: catch(sprintf('Comment on issue is being sent to [%s], comment: [%s]', $userEmail, $comment));

        $this->mailer->send($email);
    }
}
