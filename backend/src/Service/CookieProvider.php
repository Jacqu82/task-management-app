<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;

class CookieProvider
{
    private const int ACCESS_TOKEN_LIFETIME = 3600;
    private const int REFRESH_TOKEN_LIFETIME = 3600 * 24 * 30;

    public function getAccessToken(string $accessToken): Cookie
    {
        return new Cookie(
            'access_token',
            $accessToken,
            time() + self::ACCESS_TOKEN_LIFETIME,
            '/',
            null,
            false,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );
    }

    public function getRefreshToken(string $refreshToken): Cookie
    {
        return new Cookie(
            'refresh_token',
            $refreshToken,
            time() + self::REFRESH_TOKEN_LIFETIME,
            '/',
            null,
            false,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );
    }
}
