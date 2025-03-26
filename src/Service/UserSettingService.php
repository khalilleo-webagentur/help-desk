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
        $setting = $this->userSettingRepository->findOneBy(['user' => $user]);

        if (null === $setting) {
            $setting = new UserSetting();
            $this->save($setting->setUser($user));
        }

        return $setting;
    }

    /**
     * @return UserSetting[]
     */
    public function getAll(): array
    {
        return $this->userSettingRepository->findAll();
    }

    public function notifyWebmasterNewIssueAdded(UserInterface|User $user): bool
    {
        $setting = $this->getOneByUser($user);

        return $setting && true === $setting->notifyNewTicket();
    }

    public function notifyCustomerOnTicketStatusResolved(UserInterface|User $user): bool
    {
        $setting = $this->getOneByUser($user);

        return $setting && true === $setting->notifyCloseTicket();
    }

    public function save(UserSetting $model): UserSetting
    {
        $this->userSettingRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }
}
