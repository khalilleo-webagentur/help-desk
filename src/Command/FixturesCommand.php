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
            'story',
            'issue'
        ];

        foreach ($types as $type) {
            $newType = new TicketType();
            $newType->setName($type);
            $this->ticketTypesService->save($newType);
        }
    }

    private function addStatus(): void
    {
        $statuses = [
            ['Open', 'Being Processed', '#f8e45c'],
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
           ['Feature', '#408E03'],
           ['Bug', '#EA1010'],
           ['Documentation', '#00738C'],
           ['Support', '#0830F5'],
           ['Code Review', '#8A48DE'],
           ['Logic Change', '#C4390D'],
        ];

        foreach ($labels as $label) {
            $newLabel = new TicketLabel();
            $newLabel
                ->setName($label[0])
                ->setColor($label[1]);
            $this->ticketLabelsService->save($newLabel);
        }
    }
}
