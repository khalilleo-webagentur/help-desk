<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;

final readonly class MessagesService
{
    public function __construct(
        private MessageRepository $messageRepository,
    ){
    }

    public function getById(int $id): ?Message
    {
        return $this->messageRepository->find($id);
    }

    /**
     * @return Message[]
     */
    public function getAllWithLimit(int $limit): array
    {
        return $this->messageRepository->findBy([], ['isDeleted' => 'ASC', 'createdAt' => 'DESC'], $limit);
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