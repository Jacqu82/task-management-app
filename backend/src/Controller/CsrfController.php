<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfController extends AbstractController
{
    public function __construct(private readonly CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    #[Route('/api/csrf-token/{context}', name: 'csrf_token', methods: ['POST'])]
    public function getCsrfToken(string $context): JsonResponse
    {
        $response = new JsonResponse(['message' => 'CSRF token set']);
        $response->headers->set('CSRF-TOKEN', $this->csrfTokenManager->getToken($context)->getValue());

        return $response;
    }
}
