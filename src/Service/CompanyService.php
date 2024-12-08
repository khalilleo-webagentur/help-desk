<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;

final class CompanyService
{
    public function __construct(
        private CompanyRepository $companyRepository,
    ) {
    }

    public function getByName(string $name): ?Company
    {
        return $this->companyRepository->findOneBy(['name' => $name]);
    }

    public function save(Company $company): Company
    {
        $this->companyRepository->save($company->setUpdatedAt(new \DateTime()), true);
        return $company;
    }
}