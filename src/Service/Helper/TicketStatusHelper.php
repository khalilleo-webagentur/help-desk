<?php

declare(strict_types=1);

namespace App\Service\Helper;

use App\Entity\User;
use App\Helper\AppHelper;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketStatusHelper
{
    public function __construct(
        private TicketStatusService $ticketStatusService,
        private TicketService       $ticketService,
    ) {
    }

    public function getTabs(User|UserInterface $user): array
    {
        $isAdminOrEmployee = in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || $user->isNinja();

        $statusOpen = $this->ticketStatusService->getOneByName(AppHelper::STATUS_OPEN);
        $statusInProgress = $this->ticketStatusService->getOneByName(AppHelper::STATUS_IN_PROGRESS);
        $statusPending = $this->ticketStatusService->getOneByName(AppHelper::STATUS_PENDING);
        $statusResolved = $this->ticketStatusService->getOneByName(AppHelper::STATUS_RESOLVED);
        $statusClosed = $this->ticketStatusService->getOneByName(AppHelper::STATUS_CLOSED);

        $open = $isAdminOrEmployee
            ? count($this->ticketService->getAllByStatus($statusOpen))
            : count($this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $statusOpen));

        $inProgress = $isAdminOrEmployee
            ? count($this->ticketService->getAllByStatus($statusInProgress))
            : count($this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $statusInProgress));

        $pending = $isAdminOrEmployee
            ? count($this->ticketService->getAllByStatus($statusPending))
            : count($this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $statusPending));

        $resolved = $isAdminOrEmployee
            ? count($this->ticketService->getAllByStatus($statusResolved))
            : count($this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $statusResolved));

        $closed = $isAdminOrEmployee
            ? count($this->ticketService->getAllByStatus($statusClosed))
            : count($this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $statusClosed));

        $all = $open + $inProgress + $pending + $resolved + $closed;

        return [
            ['name' => AppHelper::STATUS_ALL, 'counter' => $all],
            ['name' => AppHelper::STATUS_OPEN, 'counter' => $open],
            ['name' => AppHelper::STATUS_IN_PROGRESS, 'counter' => $inProgress],
            ['name' => AppHelper::STATUS_PENDING, 'counter' => $pending],
            ['name' => AppHelper::STATUS_RESOLVED, 'counter' => $resolved],
            ['name' => AppHelper::STATUS_CLOSED, 'counter' => $closed],
        ];
    }
}