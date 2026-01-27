<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

readonly class JwtAuthService
{
	public function __construct(
		private JWTEncoderInterface $jwtEncoder,
		private CookieProvider $cookieProvider,
	) {
	}
	
	public function authenticate(User $user, JsonResponse $response): JsonResponse
	{
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
		
		$response->headers->setCookie($this->cookieProvider->getAccessToken($accessToken));
		$response->headers->setCookie($this->cookieProvider->getRefreshToken($refreshToken));
		
		return $response;
	}
}
