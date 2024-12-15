<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;

final readonly class CompanyService
{
    public function __construct(
        private CompanyRepository $companyRepository,
    ) {
    }

    public function getById(int $id): ?Company
    {
        return $this->companyRepository->find($id);
    }

    public function getByName(string $name): ?Company
    {
        return $this->companyRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return Company[]
     */
    public function getAll(): array
    {
        return $this->companyRepository->findBy([], ['name' => 'ASC']);
    }

    public function updateIsSelected(Company $company): Company
    {
        foreach ($this->getAll() as $row) {
            $this->save($row->setSelected(false));
        }

        return $this->save($company->setSelected(true));
    }


    public function save(Company $company): Company
    {
        $this->companyRepository->save($company->setUpdatedAt(new \DateTime()), true);
        return $company;
    }
}