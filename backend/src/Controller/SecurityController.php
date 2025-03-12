<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly JWTEncoderInterface $jwtEncoder,
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $user = $this->userRepository->findOneBy(['email' => $data['email']]);

        if (null === $user) {
            return new JsonResponse(['error' => 'Niepoprawny login lub hasło'], Response::HTTP_BAD_REQUEST);
        }

        $isValid = $this->userPasswordHasher->isPasswordValid($user, $data['password']);

        if (!$isValid) {
            return new JsonResponse(['error' => 'Niepoprawny login lub hasło'], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->jwtEncoder->encode([
            'email' => $user->getEmail(),
        ]);

        $response = new JsonResponse(['message' => 'Login successful'], Response::HTTP_OK);
        $cookie = new Cookie(
            'jwt_token',
            $token,
            strtotime('+1 hour'),
            '/',
            null,
            null,
            false,
            false,
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
