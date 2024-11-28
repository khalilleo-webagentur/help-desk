<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TicketLabel;
use App\Entity\TicketStatus;
use App\Entity\TicketType;
use App\Service\TicketLabelsService;
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
    public const FAILURE = 0;
    public const SUCCESS = 1;

    public function __construct(
        private readonly TicketLabelsService  $ticketLabelsService,
        private readonly TicketTypesService  $ticketTypesService,
        private readonly TicketStatusService $ticketStatusService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('running ...');

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
            ['Open', 'Being Processed', '#9d8900'],
            ['In Progress', 'We are working on this ticket', '#1c71d8'],
            ['Pending', 'Awaiting your Reply', '#e66100'],
            ['Resolved', 'This ticket has been resolved', '#2b8202'],
            ['Closed', 'This ticket has been closed', '#c01c28'],
        ];

        foreach ($statuses as $status) {
            $newStatus = new TicketStatus();
            $newStatus
                ->setName($status[0])
                ->setDescription($status[1])
                ->setColor($status[2]);
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
