<?php

declare(strict_types=1);

namespace App\Service\Core;

use Khalilleo\TokenGen\Token;

final class TokenService
{
    public function getRandomApiToken(): string
    {
        return $this->getTokenInstance()->getRandomApiToken();
    }

    private function getTokenInstance(): Token
    {
        return new Token();
    }
}
