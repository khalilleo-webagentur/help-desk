<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Service\Core\ConfigService;
use App\Service\MessagesService;
use App\Service\TicketCommentsService;
use App\Service\TicketService;
use DateTime;
use DateTimeInterface;
use Exception;
use Random\RandomException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigHelper extends AbstractExtension
{
    public function __construct(
        private readonly ConfigService          $configService,
        private readonly TicketService          $ticketService,
        private readonly TicketCommentsService  $ticketCommentsService,
        private readonly MessagesService        $messagesService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('maskEmail', [$this, 'maskEmail']),
            new TwigFunction('unseenMessages', [$this, 'getUnseenMessages']),
            new TwigFunction('hasTicketComment', [$this, 'hasTicketComment']),
            new TwigFunction('convertToHoursMinutes', [$this, 'convertToHoursMinutes']),
            new TwigFunction('role', [$this, 'getRole']),
            new TwigFunction('isSuperAdmin', [$this, 'isSuperAdmin']),
            new TwigFunction('isCustomer', [$this, 'isCustomer']),
            new TwigFunction('hash', [$this, 'hash']),
            new TwigFunction('timeAgo', [$this, 'timeAgo']),
            new TwigFunction('formatSizeUnits', [$this, 'formatSizeUnits']),
            new TwigFunction('dateTime', [$this, 'dateTime']),
            new TwigFunction('replaceLinkWithHref', [$this, 'replaceLinkInText']),
            new TwigFunction('count', [$this, 'getCount']),
            new TwigFunction('appName', [$this, 'getAppName']),
            new TwigFunction('year', [$this, 'getYear']),
            new TwigFunction('circle', [$this, 'circle']),
            new TwigFunction('checkCircle', [$this, 'checkCircle']),
            new TwigFunction('check', [$this, 'check']),
            new TwigFunction('faThumbsUp', [$this, 'getThumbsUp']),
            new TwigFunction('faThumbsDown', [$this, 'getThumbsDown']),
            new TwigFunction('phoneNumber', [$this, 'phoneNumber']),
            new TwigFunction('appAuthor', [$this, 'getAppAuthor']),
            new TwigFunction('madeBy', [$this, 'getMadeBy']),
            new TwigFunction('version', [$this, 'getVersion']),
        ];
    }

    public function maskEmail(string $email, string $replacement = '*'): string
    {
        list($username, $domain) = explode('@', $email);
        $maskedUsername = $username[0] . str_repeat($replacement, strlen($username) - 2) . $username[strlen($username) - 1];
        return $maskedUsername . '@' . $domain;
    }

    public function getUnseenMessages(string $email): array
    {
        return $this->messagesService->getAllUnSeenMessagesByIdentifier($email);
    }
    
    public function hasTicketComment(int $ticketId): bool
    {
        if ($ticket = $this->ticketService->getById($ticketId)) {
            $comment = $this->ticketCommentsService->getByTicket($ticket);
            return !empty($comment);
        }

        return false;
    }

    public function convertToHoursMinutes($time): string
    {
        if ($time < 1) {
            return '--';
        }

        $hours = floor($time / 60);
        $hours = $hours > 0 ? $hours . ' ' . ($hours == 1 ? 'hour ' : 'hours ') : "";
        $minutes = $time % 60 != 0 ? $time % 60 . ' ' . ($time % 60 == 1 ? 'minute ' : 'minutes ') : "";

        return $hours . $minutes;
    }

    public function getRole(?User $user): string
    {
        $role = $user instanceof User ? $user->getRoles()[0] : '';

        if ($role === 'ROLE_CUSTOMER') {
            $role = 'CUSTOMER';
        } elseif ($role === 'ROLE_SUPER_ADMIN') {
            $role = 'SUPER_ADMIN';
        } else {
            $role = 'USER';
        }

        return $role;
    }

    public function isCustomer(?User $user): bool
    {
        return $user && in_array('ROLE_CUSTOMER', $user->getRoles(), true);
    }

    public function isSuperAdmin(?User $user): bool
    {
        return $user && in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }

    /**
     * @throws RandomException
     */
    public function hash(?string $text = null): string
    {
        if (empty($text)) {
            return sha1(random_bytes(8));
        }

        return sha1($text);
    }

    public function timeAgo(DateTimeInterface $datetime, bool $fullDateTime = false): string
    {
        $now = new DateTime();
        $ago = '';

        try {
            $ago = new DateTime($datetime->format('Y-m-d H:i:s'));
        } catch (Exception $e) {
        }

        $differentTime = $now->diff($ago);

        $differentTime->w = floor($differentTime->d / 7);
        $differentTime->d -= $differentTime->w * 7;

        $components = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($components as $key => &$value) {
            if ($differentTime->$key) {
                $value = $differentTime->$key . ' ' . $value . ($differentTime->$key > 1 ? 's' : '');
            } else {
                unset($components[$key]);
            }
        }

        unset($value);

        if (!$fullDateTime) {
            $components = array_slice($components, 0, 1);
        }

        return $components ? implode(', ', $components) . ' ago' : 'just now';
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function dateTime(string $format = 'd.m.Y'): string
    {
        return (new DateTime('now'))->format($format);
    }

    public function getStripTags(string $text): string
    {
        return strip_tags($text);
    }

    public function replaceLinkInText(string $text): string
    {
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\(\w+\)|([^,[:punct:]\s]|/))#', $text, $matches);

        if (count($matches[0]) <= 0) {
            return $text;
        }

        $result = '';

        foreach ($matches[0] as $link) {
            $result = str_replace($link, '<a href="' . $link . '" target="_blank">' . $link . '</a>', $text);
        }

        return $result;
    }

    public function getCount(mixed $obj): int
    {
        if (is_countable($obj)) {
            return count($obj);
        }

        return 0;
    }

    public function getYear(): string
    {
        return (new DateTime())->format('Y');
    }

    public function circle(string $color): void
    {
        echo "<span class='bi bi-circle-fill fa fa-circle $color'></span>";
    }

    public function checkCircle(string $color): void
    {
        echo "<span class='bi bi-check2-circle fa fa-check-circle fs-6 $color'></span>";
    }

    public function check(string $color): void
    {
        echo "<span class='fa fa-check fs-6 $color'></span>";
    }

    public function getThumbsUp(): void
    {
        echo "<span class='fa fa-thumbs-up fa-sm text-success'></span>";
    }

    public function getThumbsDown(?string $color = null): void
    {
        $color = $color ?? 'text-danger';

        echo "<span class='fa fa-thumbs-down fa-sm $color'></span>";
    }

    public function phoneNumber(): string
    {
        return $this->configService->getParameter('phoneNumber');
    }

    public function getAppName(): string
    {
        return $this->configService->getParameter('app_name');
    }

    public function getMadeBy(): string
    {
        return $this->configService->getParameter('app_made_by');
    }

    public function getAppAuthor(): string
    {
        return $this->configService->getParameter('app_author');
    }

    public function getVersion(): string
    {
        return 'v' . $this->configService->getParameter('app_version');
    }
}
