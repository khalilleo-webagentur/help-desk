<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Message;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Helper\AppHelper;
use App\Mails\Admin\ContactFormNewMessageMail;
use App\Service\MessageContentService;
use App\Service\MessagesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/messages/e5i5y0g9v8v0l4b2')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_MESSAGES_ROUTE = 'app_dashboard_message_index';

    public function __construct(
        private readonly UserService           $userService,
        private readonly MessagesService       $messagesService,
        private readonly MessageContentService $messageContentService,
    ) {
    }

    #[Route('/b3z3d3k9/{limit?}', name: 'app_dashboard_message_index')]
    public function index(?string $limit): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $limit = $limit ? 1000 : AppHelper::DEFAULT_LIMIT_MESSAGES_ENTRIES;
        $user = $this->getUser();
        $isSuperAdmin = $this->isSuperAdmin();

        $messages = $isSuperAdmin
            ? $this->messagesService->getAllWithLimit($limit)
            : $this->messagesService->getAllByEmailWithLimit($user->getUserIdentifier(), $limit);

        $users = $isSuperAdmin ? $this->userService->getAllCustomers() : [];

        return $this->render('dashboard/messages/index.html.twig', [
            'messages' => $messages,
            'limit' => AppHelper::DEFAULT_LIMIT_MESSAGES_ENTRIES,
            'maxLimit' => AppHelper::DEFAULT_MAX_LIMIT_MESSAGES_ENTRIES,
            'users' => $users,
        ]);
    }

    #[Route('/e3t4mti7', name: 'app_dashboard_message_new', methods: ['POST'])]
    public function new(Request $request, ContactFormNewMessageMail $contactFormNewMessageMail): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $redirectTo = $this->redirectToRoute(
            $this->validate($request->request->get('_backTo'))
        );

        $subject = $this->validate($request->request->get('subject'));

        if (!$subject) {
            $this->addFlash('warning', 'Subject field is required.');
            return $redirectTo;
        }

        $message = $this->validateTextarea($request->request->get('msg'));

        if (!$message) {
            $this->addFlash('warning', 'Message field is required.');
            return $redirectTo;
        }

        $user = $this->getUser();
        $isSuperAdmin = $this->isSuperAdmin();

        if (!$this->validateCheckbox($request->request->get('addToAll'))) {
            $username = $user->getName();
            $email = $user->getUserIdentifier();

            if ($isSuperAdmin) {
                $targetUser = $this->userService->getById($this->validateNumber(
                    $request->request->get('uId')
                ));

                if (!$targetUser) {
                    $this->addFlash('warning', 'User not found.');
                    return $redirectTo;
                }

                $username = $targetUser->getName();
                $email = $targetUser->getEmail();
            }

            $messageContent = $this->messageContentService->create($message);
            $this->messagesService->create($username, $email, $subject, $messageContent);
            $this->addFlash('success', 'Your message has been sent successfully.');
        } else {

            $users = $this->userService->getAllCustomers();

            if (count($users) > 0) {
                $messageContent = $this->messageContentService->create($message);

                foreach ($users as $row) {
                    $this->messagesService->create(
                        $row->getName(),
                        $row->getEmail(),
                        $subject,
                        $messageContent,
                    );
                }
                $this->addFlash('success', 'Your message has been sent to all users.');
            }
        }

        if (!$isSuperAdmin) {
            $contactFormNewMessageMail->send([]);
        }

        return $redirectTo;
    }

    #[Route('/l8t4c3i7/{id}', name: 'app_dashboard_message_view')]
    public function view(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $messageId = $this->validateNumber($id);
        $isSuperAdmin = $this->isSuperAdmin();

        $message = $isSuperAdmin
            ? $this->messagesService->getById($messageId)
            : $this->messagesService->getByEmailAndId($user->getUserIdentifier(), $messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        if ($message->getEmail() === $user->getUserIdentifier()) {
            $this->messagesService->updateIsSeen($message);
        }

        return $this->render('dashboard/messages/view.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/d8t4c6i7/{id}', name: 'app_dashboard_message_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();
        $messageId = $this->validateNumber($id);

        $message = $this->messagesService->getById($messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        return $this->render('dashboard/messages/edit.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/sk74c3ib/{id}', name: 'app_dashboard_message_store', methods: ['POST'])]
    public function store(?string $id, Request $request): Response|RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $subject = $this->validate($request->request->get('subject'));
        $content = $this->validateTextarea($request->request->get('content'), true);

        if (!$subject || !$content) {
            $this->addFlash('warning', 'Subject and Message fields are required.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        $messageId = $this->validateNumber($id);
        $message = $this->messagesService->getById($messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        $isSeen = !$this->validateCheckbox($request->request->get('isSeen'));
        $isDeleted = $this->validateCheckbox($request->request->get('isDeleted'));

        $this->messagesService->save(
            $message
                ->setSubject($subject)
                ->setIsSeen($isSeen)
                ->setDeleted($isDeleted)
                ->setSeenAt($isSeen ? new DateTime() : null)
        );

        $this->messageContentService->save($message->getMessageContent()->setContent($content));

        $this->addFlash('success', 'Message has been saved successfully.');

        return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
    }

    #[Route('/d3t4lti7/{id}', name: 'app_dashboard_message_delete', methods: ['POST'])]
    public function delete(?string $id, Request $request): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $messageId = $this->validateNumber($id);

        $message = $this->isSuperAdmin()
            ? $this->messagesService->getById($messageId)
            : $this->messagesService->getByEmailAndId($user->getUserIdentifier(), $messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        $this->validateCheckbox($request->request->get('deletePermanently'))
            ? $this->messagesService->delete($message)
            : $this->messagesService->save($message->setDeleted(true));

        return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
    }
}
