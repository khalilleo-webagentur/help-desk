<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSetting;
use App\Repository\UserSettingRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserSettingService
{
    public function __construct(
        private UserSettingRepository $userSettingRepository,
    ) {
    }

    public function getOneByUser(User|UserInterface $user): ?UserSetting
    {
        return $this->userSettingRepository->findOneBy(['user' => $user]);
    }

    /**
     * @return UserSetting[]
     */
    public function getAll(): array
    {
        return $this->userSettingRepository->findAll();
    }

    public function notifyCustomerOnTicketStatusClosed(UserInterface|User $user): bool
    {
        $setting = $this->getOneByUser($user);

        return $setting && $setting->notifyCloseTicket();
    }

    public function save(UserSetting $model): UserSetting
    {
        $this->userSettingRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }
}
