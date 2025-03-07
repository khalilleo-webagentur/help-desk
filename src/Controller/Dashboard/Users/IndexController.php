<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Users;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\User;
use App\Helper\AppHelper;
use App\Service\CompanyService;
use App\Service\TokenGeneratorService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('dashboard/users/x9z3a4c5d9b8f0s3')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const ADMIN_USERS_ROUTE = 'app_dashboard_users_index';
    private const ADMIN_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService           $userService,
        private readonly CompanyService        $companyService,
        private readonly TokenGeneratorService $tokenGeneratorService,
    ) {
    }

    #[Route('/home', name: 'app_dashboard_users_index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);

        if (!$isAdmin && !$user->isTeamLeader()) {
            return $this->redirectToRoute(self::ADMIN_TICKETS_ROUTE);
        }

        $company = $this->companyService->getById($this->validateNumber(
            $request->get('p7x5a8e9')
        ));

        $users = $isAdmin
            ? $this->userService->getAllOrAllByCompany($company)
            : $this->userService->getAllByCompany($user->getCompany());

        $hasToken = $this->validateCheckbox($request->get('hasToken'));
        $usersByFilter = [];

        if ($hasToken) {
            foreach ($users as $user) {
                if (!empty($user->getToken())) {
                    $usersByFilter[] = $user;
                }
            }
        }

        $isAdmin && $company
            ? $this->companyService->updateIsSelected($company)
            : $this->companyService->updateIsSelected(null);

        $companies = $this->companyService->getAll();

        return $this->render('dashboard/users/index.html.twig', [
            'users' => $hasToken ? $usersByFilter : $users,
            'roles' => $isAdmin ? AppHelper::ROLES : [],
            'companies' => $isAdmin ? $companies : [],
            'selectedCompanyId' => $company ? $company->getId() : 0,
        ]);
    }

    #[Route('/add', name: 'app_dashboard_user_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $name = $this->validate($request->request->get('name'));
        $email = $this->validateEmail($request->request->get('email'));

        if (!$name || !$email) {
            $this->addFlash('warning', 'Name and Email are required.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $user = $this->userService->getByEmail($email);

        if ($user) {
            $this->addFlash('warning', 'User can not be added. E-0001');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $currentUser = $this->getUser();
        $isAdmin = $this->userService->isAdmin($currentUser);
        $iCompany = $this->validateNumber($request->request->get('company'));

        if ($isAdmin && $iCompany <= 0) {
            $this->addFlash('warning', 'Company is required.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $company = $isAdmin ? $this->companyService->getById($iCompany) : $currentUser->getCompany();

        $isTeamLeader = $isAdmin && count($this->userService->getAllTeamLeadersWithinACompany($company)) === 0;

        $token = $this->tokenGeneratorService->randomToken();

        $user = new User();

        $this->userService->save(
            $user
                ->setName($this->validateNameAndReplaceSpace($name, '.'))
                ->setEmail($email)
                ->setCompany($company)
                ->setPassword($this->userService->encodePassword($email))
                ->setToken($token)
                ->setRoles([AppHelper::ROLE_CUSTOMER])
                ->setIsVerified(true)
                ->setTeamLeader($isTeamLeader)
                ->setNinja(true === $currentUser->isNinja())
        );

        $companyId = $this->validateNumber($request->request->get('p7x5a8e9'));

        $this->addFlash('success', 'User has been added.');

        return $this->redirectToRoute(self::ADMIN_USERS_ROUTE, ['p7x5a8e9' => $companyId]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_user_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);

        if (!$isAdmin && !$user->isTeamLeader()) {
            return $this->redirectToRoute(self::ADMIN_TICKETS_ROUTE);
        }

        $targetUser = $this->userService->getById(
            $this->validateNumber($id)
        );

        if (!$targetUser) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        if (!$isAdmin && ($targetUser->getCompany() !== $user->getCompany())) {
            $this->addFlash('warning', 'User could not be found. E-0002');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'user' => $targetUser,
            'roles' => $isAdmin ? AppHelper::ROLES : [],
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_user_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $currentUser = $this->getUser();
        $isAdmin = $this->userService->isAdmin($currentUser);

        if (!$isAdmin && !$currentUser->isTeamLeader()) {
            return $this->redirectToRoute(self::ADMIN_TICKETS_ROUTE);
        }

        $name = $this->validate($request->request->get('name'));

        $email = $this->validateEmail($request->request->get('email'));

        if (!$name || !$email) {
            $this->addFlash('warning', 'Name and Email are required.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $user = $this->userService->getById($this->validateNumber($id));

        if (!$user) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        if (!$isAdmin && $this->userService->isAdmin($user)) {
            $this->addFlash('warning', 'You don not have enough permission to update admin details. E-0003');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        if (!$isAdmin && ($currentUser->getCompany() !== $user->getCompany())) {
            $this->addFlash('warning', 'User could not be found. E-0002');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        if ($email != $user->getEmail() && null !== $this->userService->getByEmail($email)) {
            $this->addFlash('warning', 'User can not be added. E-0001');
            return $this->redirectToRoute(self::ADMIN_USERS_ROUTE);
        }

        $isVerified = $this->validateCheckbox($request->request->get('isVerified'));
        $token = $this->validate($request->request->get('token'));
        $isDeleted = $this->validateCheckbox($request->request->get('isDeleted'));

        $verified = ($isAdmin
            || $currentUser->isTeamLeader())
        && !in_array(AppHelper::ROLE_SUPER_ADMIN, $user->getRoles(), true
        ) ? $isVerified
            : $user->isVerified();

        if ($isDeleted) {
            $verified = false;
            $token = null;
        }

        $iRole = $this->validate($request->request->get('4yt4bG2'));
        $role = $isAdmin && in_array($iRole, array_keys(AppHelper::ROLES), true)
            ? ['ROLE_' . $iRole]
            : $user->getRoles();


        $isTeamLeader = $this->validateCheckbox($request->request->get('x3h1r9u2'));

        if ($isAdmin) {
            $this->userService->changeUserPositionToTeamLeader($user, $isTeamLeader);
        }

        $isEmployee = $this->validateCheckbox($request->request->get('c1z3n6t4'));

        $this->userService->save(
            $user
                ->setName($this->validateNameAndReplaceSpace($name, '.'))
                ->setEmail($email)
                ->setPassword($this->userService->encodePassword($email))
                ->setToken($token)
                ->setIsVerified($verified)
                ->setNinja($isAdmin ? $isEmployee : false)
                ->setRoles($role)
                ->setDeleted($isDeleted)
        );

        $companyId = $this->validate($request->request->get('p7x5a8e9'));

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::ADMIN_USERS_ROUTE, ['p7x5a8e9' => $companyId]);
    }
}
