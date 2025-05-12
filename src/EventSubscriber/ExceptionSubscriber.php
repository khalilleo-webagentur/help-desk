<?php

namespace App\EventSubscriber;

use App\Helper\AppHelper;
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
        $this>$this->systemLogsService->create(AppHelper::SYSTEM_LOG_EVENT_EXCEPTION, $message);
        $this->monolog->logger->warning($message);
    }
}
