<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepository,
    ) {
    }

    public function getById(int $id): ?Project
    {
        return $this->projectRepository->findOneBy(['id' => $id]);
    }

    public function getByCustomerAndId(UserInterface $user, int $id): ?Project
    {
        return $this->projectRepository->findOneBy(['customer' => $user, 'id' => $id]);
    }

    public function getOneByCustomer(User $user): ?Project
    {
        return $this->projectRepository->findOneBy(['customer' => $user]);
    }

    public function getOneByTitle(string $title): ?Project
    {
        return $this->projectRepository->findOneBy(['title' => $title]);
    }

    /**
     * @return Project[]
     */
    public function getAllByCustomer(UserInterface $user): array
    {
        return $this->projectRepository->findBy(['customer' => $user]);
    }

    /**
     * @return Project[]
     */
    public function getAll(): array
    {
        return $this->projectRepository->findAll();
    }

    public function save(Project $model): Project
    {
        $this->projectRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function delete(Project $model): void
    {
        $this->projectRepository->remove($model, true);
    }
}
