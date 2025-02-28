<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;

final readonly class MessagesService
{
    public function __construct(
        private MessageRepository $messageRepository,
        private UserService $userService,
    ){
    }

    public function getById(int $id): ?Message
    {
        return $this->messageRepository->find($id);
    }

    public function getByEmailAndId(string $email, int $id): ?Message
    {
        return $this->messageRepository->findOneBy(['email' => $email, 'id' => $id]);
    }

    /**
     * @return Message[]
     */
    public function getAllByEmailWithLimit(string $email, int $limit): array
    {
        return $this->messageRepository->findBy(['email' => $email], ['isDeleted' => 'ASC', 'createdAt' => 'DESC'], $limit);
    }

    /**
     * For super-admin
     * @return Message[]
     */
    public function getAllWithLimit(int $limit): array
    {
        return $this->messageRepository->findBy([], ['isDeleted' => 'ASC', 'isSeen' => 'ASC', 'createdAt' => 'DESC'], $limit);
    }

    public function getAllUnSeenMessagesByIdentifier(string $email): array
    {
        $user = $this->userService->getByEmail($email);

        if (!$user) {
            return [
                'count' => 0,
                'lastMessageDateTime' => null
            ];
        }

        $messages = $this->userService->isAdmin($user)
            ? $this->messageRepository->findBy(['isSeen' => 0], ['createdAt' => 'DESC'])
            : $this->messageRepository->findBy(['email' => $email, 'isSeen' => 0], ['createdAt' => 'DESC']);

        $counter = count($messages);

        return [
            'count' => $counter,
            'lastMessageDateTime' => $counter > 0 ? $messages[0]->getCreatedAt() : null,
        ];
    }

    public function create(string $name, string $email, string $subject, string $message): Message
    {
        $messageModel = new Message();

        $messageModel
            ->setName($name)
            ->setEmail($email)
            ->setSubject($subject)
            ->setMessage($message);

        $this->save($messageModel);

        return $messageModel;
    }

    public function updateIsSeen(Message $message): void
    {
        if (!$message->isSeen()) {
            $this->save($message->setIsSeen(true));
        }
    }

    public function save(Message $message): Message
    {
        $this->messageRepository->save($message->setUpdatedAt(new \DateTime()), true);
        return $message;
    }

    public function delete(Message $message): void
    {
        $message->setDeleted(true);
        $this->save($message);
    }
}