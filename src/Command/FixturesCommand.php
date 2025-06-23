<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TicketLabel;
use App\Entity\TicketPriority;
use App\Entity\TicketStatus;
use App\Entity\TicketType;
use App\Helper\AppHelper;
use App\Service\TicketLabelsService;
use App\Service\TicketPriorityService;
use App\Service\TicketStatusService;
use App\Service\TicketTypesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// $ php bin/console app:seeds

#[AsCommand(
    name: 'app:seeds',
    description: 'Add standard data.',
    hidden: false
)]
class FixturesCommand extends Command
{
    public const int FAILURE = 0;
    public const int SUCCESS = 1;

    public function __construct(
        private readonly TicketPriorityService $ticketPriorityService,
        private readonly TicketLabelsService $ticketLabelsService,
        private readonly TicketTypesService  $ticketTypesService,
        private readonly TicketStatusService $ticketStatusService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $ php bin/console app:seeds

        $output->writeln('running ...');

        $this->addProjects();
        $this->addPriorities();
        $this->addType();
        $this->addStatus();
        $this->addLabels();

        $output->writeln('Done!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        //
    }

    private function addProjects(): void
    {
        // add projects manually ..
    }

    private function addPriorities(): void
    {
        $priorities = [
            [AppHelper::PRIORITY_URGENT, 'is reserved for critical issues that require immediate attention', 'red'],
            [AppHelper::PRIORITY_HIGH, 'is reserved for critical issues that require attention', 'orange'],
            [AppHelper::PRIORITY_MEDIUM, 'are important but not as time-sensitive as high-priority ones', 'green'],
            [AppHelper::PRIORITY_LOW, ' is typically assigned to non-urgent inquiries, general questions or feature requests', 'blue']
        ];

        foreach ($priorities as $priority) {
            $newPriority = new TicketPriority();
            $newPriority
                ->setName($priority[0])
                ->setDescription($priority[1])
                ->setTextColor($priority[2]);
            $this->ticketPriorityService->save($newPriority);
        }
    }

    private function addType(): void
    {
        $types = [
            ['Tasks', 'A specific tasks which should not take more than 1 working day'],
            ['Stories', 'Entail the most important project information'],
            ['Epic', 'An Epic could present the topic to which a task belongs or represent a big project']
        ];

        foreach ($types as $type) {
            $newType = new TicketType();
            $newType
                ->setName($type[0])
                ->setDescription($type[1]);
            $this->ticketTypesService->save($newType);
        }
    }

    private function addStatus(): void
    {
        $statuses = [
            [AppHelper::STATUS_OPEN, 'The ticket has been created but not yet addressed.', '#e5a50a', 1],
            [AppHelper::STATUS_IN_PROGRESS, 'The ticket is currently being addressed, with active efforts to resolve the issue or fulfill the request.', '#1c71d8', 2],
            [AppHelper::STATUS_PENDING, 'The ticket is awaiting action or information, either from customer or another team.', '#e66100', 6],
            [AppHelper::STATUS_ESCALATED, 'The issue has been raised to a higher level of support or management for further attention.', '#752424', 3],
            [AppHelper::STATUS_HOLD, 'Work on ticket has been temporarily paused.', '#c27448', 5],
            [AppHelper::STATUS_WAITING_FOR_CUSTOMER, 'The ticket is in a pending state, awaiting a response or action from customer.', '#21959c', 4],
            [AppHelper::STATUS_REOPENED, 'The ticket is reopened because the issue was fully resolved or has recurred.', '#968f27', 9],
            [AppHelper::STATUS_RESOLVED, 'The issue has been fixed or the request has been fulfilled.', '#2b8202', 7],
            [AppHelper::STATUS_CLOSED, 'The ticket has been resolved and no further action is required.', '#c01c28', 8],
        ];

        foreach ($statuses as $status) {
            $newStatus = new TicketStatus();
            $newStatus
                ->setName($status[0])
                ->setDescription($status[1])
                ->setColor($status[2])
                ->setPosition($status[3]);
            $this->ticketStatusService->save($newStatus);
        }
    }

    private function addLabels(): void
    {
        $labels = [
            ['Feature', '#408E03', 'Change Request'],
            ['Bug', '#EA1010', 'Any technical issue'],
            ['Documentation', '#00738C', 'Software Documentation'],
            ['Support', '#0830F5', 'Action not taking too much time'],
            ['Logic Change', '#C4390D', 'Change existing logic'],
        ];

        foreach ($labels as $label) {
            $newLabel = new TicketLabel();
            $newLabel
                ->setName($label[0])
                ->setColor($label[1])
                ->setDescription($label[2]);
            $this->ticketLabelsService->save($newLabel);
        }
    }
}
