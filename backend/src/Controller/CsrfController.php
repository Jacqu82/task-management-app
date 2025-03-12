<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfController extends AbstractController
{
    public function __construct(private readonly CsrfTokenManagerInterface $csrfTokenManager) {}

    #[Route('/api/csrf-token', name: 'csrf_token', methods: ['GET'])]
    public function getCsrfToken(): JsonResponse
    {
        return $this->json(['csrf_token' => $this->csrfTokenManager->getToken('register')->getValue()]);
    }
}
