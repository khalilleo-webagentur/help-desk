<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\Utilities\ApiAbstractController;
use App\Service\Core\MonologService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v1/users/')]
final class ClearTokenApiController extends ApiAbstractController
{
    public function __construct(
        private readonly UserService    $userService,
        private readonly MonologService $monologService,
    ) {
    }

    /**
     * Retrieve users with tokens that are older than 15 minutes and clear token if any.
     */
    #[Route('clear_token', name: 'app_api_clear_token', methods: ['POST'])]
    public function clearToken(Request $request): Response
    {
        $token = $this->getAgentToken($request);

        if (!$this->hasHeaderUserAgent($request)) {
            return $this->snedResponseMissingOrInvalidUserAgent();
        }

        if (empty($token) || $token !== $this->getApiKeyConfig()) {
            return $this->snedResponseNotAuthorized();
        }

        $count = $this->userService->clearToken();

        if ($count > 0) {
            $this->monologService->logger->debug(
                sprintf('Api endpoint remove users token (%s) after 15 minutes.', $count)
            );
        }

        $response = [
            'success' => $count > 0,
            'message' => $count > 0 ? 'Token cleared successfully.' : 'No token has been cleared.',
            'data' => $count > 0 ? $count : null,
        ];

        return $this->json($response);
    }
}