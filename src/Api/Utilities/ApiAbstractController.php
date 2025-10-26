<?php

declare(strict_types=1);

namespace App\Api\Utilities;

use App\Traits\FormValidationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAbstractController extends AbstractController
{
    use FormValidationTrait;
    protected function isApiKeyValid(Request $request): bool
    {
        $apiKey = $request->headers->get('Authorization');

        if (empty($apiKey) || !$request->headers->has('Authorization') || !str_starts_with($apiKey, 'Bearer ')) {
            return false;
        }

        $token = substr($apiKey, 7);

        if (empty($token) || strlen($token) !== 36) {
            return false;
        }

        return true;
    }

    protected function hasHeaderUserAgent(Request $request): bool
    {
        $userAgent = $request->headers->get('User-Agent');

        if (empty($userAgent) || !$request->headers->has('User-Agent')) {
            return false;
        }

        if ($userAgent !== $this->getUserAgentConfig()) {
            return false;
        }

        return true;
    }

    protected function snedResponseNotAuthorized(): Response
    {
        return $this->json([
            'success' => false,
            'message' => 'Not authorized'
        ], Response::HTTP_UNAUTHORIZED);
    }

    protected function snedResponseMissingOrInvalidUserAgent(): Response
    {
        return $this->json([
            'success' => false,
            'message' => 'User-agent missing or invalid.',
        ], Response::HTTP_UNAUTHORIZED);
    }

    protected function getAgentToken(Request $request): string
    {
        if (!$this->isApiKeyValid($request)) {
            return '';
        }

        $apiKey = $request->headers->get('Authorization');
        return substr($apiKey, 7);
    }

    protected function getUserAgentConfig(): string
    {
        return $this->getParameter('api_user_agent');
    }

    protected function getApiKeyConfig(): string
    {
        return $this->getParameter('api_key');
    }
}