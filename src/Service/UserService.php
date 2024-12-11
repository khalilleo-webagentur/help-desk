<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function getById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function getByToken(string $token): ?User
    {
        return $this->userRepository->findOneBy(['token' => $token]);
    }

    public function getOneByCompany(Company $company): ?User
    {
        return $this->userRepository->findOneBy(['company' => $company]);
    }

    /**
     * @return User[]
     */
    public function getAllByCompany(Company $company): array
    {
        return $this->userRepository->findBy(['company' => $company]);
    }

    /**
     * @return User[]
     */
    public function getAllOrAllByCompany(?Company $company): array
    {
        if ($company !== null) {
            return $this->userRepository->findBy(['company' => $company]);
        }

        return $this->getAll();
    }

    /**
     * @return User[]
     */
    public function getAllCustomers(): array
    {
        $customers = [];

        foreach ($this->getAll() as $user) {
            if (in_array('ROLE_CUSTOMER', $user->getRoles(), true)) {
                $customers[] = $user;
            }
        }

        return $customers;
    }

    /**
     * @return User[]
     */
    public function getAllEmployees(): array
    {
        $employees = [];

        foreach ($this->getAll() as $user) {
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
                $employees[] = $user;
            }
        }

        return $employees;
    }

    /**
     * @return User[]
     */
    public function getAll(): array
    {
        return $this->userRepository->findBy([], ['company' => 'ASC', 'name' => 'ASC']);
    }

    public function isAdmin(?UserInterface $user): bool
    {
        if (null === $user) {
            return false;
        }

        return in_array('ROLE_SUPER_ADMIN', $user->getRoles());
    }

    public function isCustomer(UserInterface $user): bool
    {
        return in_array('ROLE_CUSTOMER', $user->getRoles());
    }

    public function changeUserPositionToTeamLeader(UserInterface $targetUser): void
    {
        foreach ($this->getAllByCompany($targetUser->getCompany()) as $user) {

            $user->setTeamLeader(false);

            if ($user->getId() === $targetUser->getId()) {
                $user->setTeamLeader(true);
            }

            $this->save($user);
        }
    }

    public function save(User $model): ?User
    {
        $this->userRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function encodePassword(string $text): string
    {
        return password_hash($text, PASSWORD_DEFAULT);
    }

    public function isPasswordValid(User $user, string $text): bool
    {
        return password_verify($text, $user->getPassword());
    }
}
