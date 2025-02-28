<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\MessageContent;
use App\Repository\MessageContentRepository;

final readonly class MessageContentService
{
    public function __construct(
        private MessageContentRepository $messageContentRepository,
    ) {
    }

    public function create(string $content): MessageContent
    {
        $message = new MessageContent();
        $message->setContent($content);
        $this->save($message);

        return $message;
    }

    public function save(MessageContent $messageContent): MessageContent
    {
        $this->messageContentRepository->save($messageContent, true);
        return $messageContent;
    }
}