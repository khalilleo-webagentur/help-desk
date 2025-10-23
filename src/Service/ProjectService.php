<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
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

    public function getById(int $id): ?Project
    {
        return $this->projectRepository->findOneBy(['id' => $id]);
    }

    public function getByCompanyAndId(Company $company, int $id): ?Project
    {
        return $this->projectRepository->findOneBy(['company' => $company, 'id' => $id]);
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
    public function getAllByCompany(Company $company): array
    {
        return $this->projectRepository->findBy(['company' => $company]);
    }

    /**
     * @return Project[]
     */
    public function getAll(): array
    {
        return $this->projectRepository->findBy([], ['company' => 'DESC', 'title' => 'ASC']);
    }

    public function save(Project $model): Project
    {
        $this->projectRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function delete(Project $model, bool $flush): void
    {
        $this->projectRepository->remove($model, $flush);
    }
}
