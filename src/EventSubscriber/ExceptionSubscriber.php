<?php

namespace App\EventSubscriber;

use App\Service\Core\MonologService;
use App\Service\SystemLogsService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SystemLogsService $systemLogsService,
        private MonologService    $monolog
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [['logException', 0]],
        ];
    }

    public function logException(ExceptionEvent $event): void
    {
        $message = $event->getThrowable()->getMessage();
        $line = $event->getThrowable()->getLine();
        $file = basename($event->getThrowable()->getFile());

        $message = sprintf('%s, %s and L.%s', $message, $file, $line);

        $this>$this->systemLogsService->create($message);
        $this->monolog->logger->warning($message);
    }
}
