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

    public function getById(int $id): ?SystemLog
    {
        return $this->systemLogRepository->find($id);
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

    public function deleteAllByCriteria(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        $i = 0;

        foreach ($this->systemLogRepository->findTDeleteAllByCriteria($from, $to) as $systemLog) {
            $this->delete($systemLog);
            $i++;
        }

        return $i;
    }

    public function delete(SystemLog $systemLog): void
    {
        $this->systemLogRepository->remove($systemLog, true);
    }
}