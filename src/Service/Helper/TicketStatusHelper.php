<?php

declare(strict_types=1);

namespace App\Service\Helper;

use App\Entity\User;
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

        $tabs = [];
        $countAll = 0;

        $tabs[] = ['name' => 'all', 'counter' => &$countAll];

        foreach ($this->ticketStatusService->getAll() as $ticketStatus) {

            $countTickets = $isAdminOrEmployee
                ? count($this->ticketService->getAllByStatus($ticketStatus))
                : count($this->ticketService->getAllByCompanyAndStatus($user->getCompany(), $ticketStatus));

            $countAll += $countTickets;

            $tabs[] = [
                'name' => strtolower($ticketStatus->getName()),
                'counter' => $countTickets,
            ];
        }

        return $tabs;
    }
}