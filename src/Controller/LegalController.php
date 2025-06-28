<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Core\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    public function __construct(
        private readonly ConfigService $configService
    ) {
    }

    #[Route('/imprint/i7l0qxk9e7o1z3r9', name: 'app_legal_imprint')]
    public function imprint(): Response
    {
        $generalManager = $this->configService->getParameter('generalManager');
        $taxNumber = $this->configService->getParameter('taxNumber');
        $street = $this->configService->getParameter('addressStreet');
        $plz = $this->configService->getParameter('addressPlz');
        $city = $this->configService->getParameter('addressCity');
        $country = $this->configService->getParameter('addressCountry');
        $phoneNumber = $this->configService->getParameter('phoneNumber');
        $email = $this->configService->getParameter('legalEmail');
        $poweredBy = $this->configService->getParameter('app_made_by');

        return $this->render('static/legal/imprint.html.twig', [
            'generalManager' => $generalManager,
            'taxNumber' => $taxNumber,
            'address' => $street . ', ' . $plz . ' ' . $city . ' ' . $country,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
            'poweredBy' => $poweredBy,
        ]);
    }

    #[Route('/privacy-policy/y7l0q6k9e1o1z3r5', name: 'app_legal_privacy_policy')]
    public function privacyPolicy(): Response
    {
        $street = $this->configService->getParameter('addressStreet');
        $plz = $this->configService->getParameter('addressPlz');
        $city = $this->configService->getParameter('addressCity');
        $country = $this->configService->getParameter('addressCountry');
        $phoneNumber = $this->configService->getParameter('phoneNumber');
        $email = $this->configService->getParameter('legalEmail');

        return $this->render('static/legal/privacy-policy.html.twig', [
            'street' => $street,
            'plz' => $plz,
            'city' => $city,
            'country' => $country,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
        ]);
    }
}
