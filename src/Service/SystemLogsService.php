<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SystemLog;
use App\Repository\SystemLogRepository;

final readonly class SystemLogsService
{
    public function __construct(
        private SystemLogRepository $systemLogRepository,
    ) {
    }

    /**
     * @return SystemLog[]
     */
    public function getAllWithLimit(int $limit): array
    {
        return $this->systemLogRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function create(string $message): void
    {
        $systemLog = new SystemLog();
        $systemLog->setMessage($message);
        $this->systemLogRepository->save($systemLog, true);
    }
}