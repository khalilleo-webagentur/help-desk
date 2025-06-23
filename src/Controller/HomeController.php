<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\AppHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'faqs' => AppHelper::FAQS
        ]);
    }
}
