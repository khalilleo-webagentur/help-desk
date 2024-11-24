<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Users;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('dashboard/users/c2o5i0p7v6s5p4w5')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const ADMIN_USERS_ROUTE = 'app_dashboard_users_index';

    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    #[Route('/home', name: 'app_dashboard_users_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $users = $this->userService->getAll();

        return $this->render('dashboard/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_user_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $user = $this->userService->getById(
            $this->validateNumber($id)
        );

        if (!$user) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_user_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $name = $this->validate($request->request->get('name'));

        $email = $this->validateEmail($request->request->get('email'));

        if (!$name || !$email) {
            $this->addFlash('warning', 'Name and Email are required.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $token = $this->validate($request->request->get('token'));

        $user = $this->userService->getById(
            $this->validateNumber($id)
        );

        if (!$user) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $isVerified = $this->validateCheckbox($request->request->get('isVerified'));

        if (!$isVerified) {
            $token = null;
        }

        $isDeleted = $this->validateCheckbox($request->request->get('isDeleted'));

        if ($isDeleted) {
            $isVerified = false;
            $token = null;
        }

        $this->userService->save(
            $user
                ->setName($name)
                ->setEmail($email)
                ->setPassword($this->userService->encodePassword($email))
                ->setToken($token)
                ->setIsVerified($isVerified)
                ->setDeleted($isDeleted)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
    }
}
