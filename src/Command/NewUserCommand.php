<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Entity\UserSetting;
use App\Service\TokenGeneratorService;
use App\Service\UserService;
use App\Service\UserSettingService;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// $ php bin/console app:new-user

#[AsCommand(
    name: 'app:new-user',
    description: 'Add new user.',
    hidden: false
)]
class NewUserCommand extends Command
{
    public const FAILURE = 0;
    public const SUCCESS = 1;

    public function __construct(
        private readonly UserService $userService,
        private readonly TokenGeneratorService $tokenGeneratorService,
        private readonly UserSettingService $userSettingService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $ php bin/console app:new-user

        $output->writeln('running ...');

        $faker = Factory::create();

        $email = $faker->safeEmail();

        if (!$this->userService->getByEmail($email)) {

            $user = new User();

            $code = $this->tokenGeneratorService->randomToken();

            $this->userService->save(
                $user
                    ->setName($faker->name())
                    ->setEmail($email)
                    ->setPassword($this->userService->encodePassword($email))
                    ->setRoles(['ROLE_SUPER_ADMIN']) // ROLE_CUSTOMER, ROLE_SUPER_ADMIN, ROLE_USER
                    ->setIsVerified(true)
                    ->setToken($code)
            );

            $userSetting = new UserSetting();

            $this->userSettingService->save($userSetting->setUser($user));

            $output->writeln(sprintf('User added. E:: %s and OTP:: %s', $email, $code));

            return self::SUCCESS;
        }

        $output->writeln('User cannot be created ...');

        return self::FAILURE;
    }

    protected function configure(): void
    {
        //
    }
}
