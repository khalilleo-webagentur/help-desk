<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Entity\MessageContent;
use App\Repository\MessageRepository;

final readonly class MessagesService
{
    public function __construct(
        private MessageRepository $messageRepository,
        private UserService       $userService,
    ) {
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
        $criteria = ['email' => $email, 'isDeleted' => 0];
        $orderBy = ['isDeleted' => 'ASC', 'createdAt' => 'DESC'];

        return $this->messageRepository->findBy($criteria, $orderBy, $limit);
    }

    /**
     * For super-admin
     * @return Message[]
     */
    public function getAllWithLimit(int $limit): array
    {
        $orderBy = ['isDeleted' => 'ASC', 'isSeen' => 'ASC', 'createdAt' => 'DESC'];

        return $this->messageRepository->findBy([], $orderBy, $limit);
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

    /**
     * Delete all messages that have the flag `is_deleted`.
     * @return Message[]
     */
    public function getAllHaveDeletedFlag(): array
    {
        return $this->messageRepository->findBy(['isDeleted' => 1]);
    }

    public function create(string $name, string $email, string $subject, MessageContent $content): Message
    {
        $message = new Message();
        $message
            ->setName($name)
            ->setEmail($email)
            ->setSubject($subject)
            ->setMessageContent($content);

        $this->save($message);

        return $message;
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

    public function emptyPin(): int
    {
        $countDeletedMessages = 0;

        foreach ($this->getAllHaveDeletedFlag() as $message) {
            $this->delete($message);
            $countDeletedMessages++;
        }

        return $countDeletedMessages;
    }

    public function delete(Message $message): void
    {
        $this->messageRepository->remove($message, true);
    }
}