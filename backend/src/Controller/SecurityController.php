<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CookieProvider;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly JWTEncoderInterface $jwtEncoder,
        private readonly CookieProvider $cookieProvider,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $csrfToken = $request->headers->get('CSRF-TOKEN');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('login', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $user = $this->userRepository->findOneBy(['email' => $data['email']]);

        if (null === $user) {
            return new JsonResponse(['error' => 'Niepoprawny login lub hasło'], Response::HTTP_BAD_REQUEST);
        }

        $isValid = $this->userPasswordHasher->isPasswordValid($user, $data['password']);

        if (!$isValid) {
            return new JsonResponse(['error' => 'Niepoprawny login lub hasło'], Response::HTTP_BAD_REQUEST);
        }

        $accessToken = $this->jwtEncoder->encode([
            'username' => $user->getEmail(),
            'exp' => time() + 3600,
            'type' => 'access'
        ]);

        $refreshToken = $this->jwtEncoder->encode([
            'username' => $user->getEmail(),
            'exp' => time() + 3600 * 24 * 30,
            'type' => 'refresh'
        ]);

        $response = new JsonResponse(['status' => 'success'], Response::HTTP_OK);
        $response->headers->setCookie($this->cookieProvider->getAccessToken($accessToken));
        $response->headers->setCookie($this->cookieProvider->getRefreshToken($refreshToken));

        return $response;
    }

    #[Route('/api/me', name: 'api_me', methods: ['POST'])]
    public function getAuthenticatedUser(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null === $user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['email' => $user->getEmail()], Response::HTTP_OK);
    }

    #[Route('/api/refresh-token', name: 'api_refresh_token', methods: ['POST'])]
    public function refreshToken(Request $request): JsonResponse
    {
        $refreshToken = $request->cookies->get('refresh_token');

        if (!$refreshToken) {
            return new JsonResponse(['message' => 'Brak refresh tokena'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = $this->jwtEncoder->decode($refreshToken);

            if (!isset($decoded['type']) || $decoded['type'] !== 'refresh') {
                return new JsonResponse(['message' => 'Nieprawidłowy typ tokena'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Nieprawidłowy refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $newAccessToken = $this->jwtEncoder->encode([
            'username' => $decoded['username'],
            'exp' => time() + 3600,
            'type' => 'access'
        ]);

        $response = new JsonResponse(['message' => 'Token odświeżony'], Response::HTTP_OK);
        $response->headers->setCookie($this->cookieProvider->getAccessToken($newAccessToken));

        return $response;
    }

    #[Route('/api/logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $response = new JsonResponse(['message' => 'Wylogowano']);

        if ($request->cookies->has('access_token')) {
            $response->headers->clearCookie('access_token');
        }

        if ($request->cookies->has('refresh_token')) {
            $response->headers->clearCookie('refresh_token');
        }

        return $response;
    }
}
