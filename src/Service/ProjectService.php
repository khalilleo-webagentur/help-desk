<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use DateTime;

final readonly class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepository,
    ) {
    }

    public function getOneByUser(User $user): ?Project
    {
        return $this->projectRepository->findOneBy(['user' => $user]);
    }

    public function save(Project $model): Project
    {
        $this->projectRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }
}
