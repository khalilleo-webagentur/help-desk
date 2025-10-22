<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SystemLog;
use App\Repository\SystemLogRepository;
use DateTimeInterface;

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

    public function getOneByMessage(string $message): ?SystemLog
    {
        return $this->systemLogRepository->findOneBy(['message' => $message]);
    }

    /**
     * @return SystemLog[]
     */
    public function getAllWithLimit(int $limit): array
    {
        return $this->systemLogRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * Filter
     * @return SystemLog[]
     */
    public function getAllByCriteria(string $event, DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->systemLogRepository->findAllByCriteria($event, $from, $to);
    }

    public function create(string $event, string $message): void
    {
        if ($this->getOneByMessage($message)) return;

        $systemLog = new SystemLog();
        $systemLog
            ->setEvent($event)
            ->setMessage($message);

        $this->systemLogRepository->save($systemLog, true);
    }

    public function deleteAllByCriteria(string $event, DateTimeInterface $from, DateTimeInterface $to): int
    {
        $i = 0;

        foreach ($this->getAllByCriteria($event, $from, $to) as $systemLog) {
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