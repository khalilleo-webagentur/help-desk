<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Users;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\Jobs\UserDeletionJob;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/users/job/p3c0s6h5z5k7x5rx')]
class JobsController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string APP_DASHBOARD_USERS_INDEX = 'app_dashboard_users_index';

    public function __construct(
        private readonly UserService            $userService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/delete/user/{id}', name: 'app_dashboard_users_job_delete_user', methods: 'POST')]
    public function delete(?string $id, Request $request, UserDeletionJob $userDeletionJob): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        $password = $this->validate($request->request->get('pw', ''));

        if (!$this->userService->isPasswordValid($user, $password)) {
            $this->addFlash('danger', 'Password is not correct. Please type your password to continue.');
            return $this->redirectToRoute(self::APP_DASHBOARD_USERS_INDEX);
        }

        $targetUser = $this->userService->getById(
            $this->validateNumber($request->request->get('uId')),
        );

        if (!$targetUser || $this->validateNumber($id) !== $targetUser->getId()) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::APP_DASHBOARD_USERS_INDEX);
        }

        $userDeletionJob->deleteEverythingRelatedToUser($targetUser, $this->entityManager)
            ? $this->addFlash('success', 'The user and its related data has been successfully deleted.')
            : $this->addFlash('warning', 'The user and its related data could not be deleted. This user may have Tickets or have assigned issues.');

        return $this->redirectToRoute(self::APP_DASHBOARD_USERS_INDEX);
    }
}
