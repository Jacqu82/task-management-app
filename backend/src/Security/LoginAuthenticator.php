<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private JWTEncoderInterface $jwtEncoder;
    private UserRepository $userRepository;

    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        UserRepository $userRepository,
    ) {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        return $request->cookies->has('access_token');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->cookies->get('access_token');

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('Brak tokena');
        }

        try {
            $data = $this->jwtEncoder->decode($apiToken);
        } catch (JWTDecodeFailureException $exception) {
            throw new CustomUserMessageAuthenticationException('Nieprawidłowy token');
        }

        $user = $this->userRepository->findOneBy(['email' => $data['username']]);

        return new SelfValidatingPassport(
            new UserBadge(
                $apiToken, function () use ($user): UserInterface {
                    return $user;
                }
            ),
            []
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }


    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $message = $authException ? $authException->getMessage() : 'Nieprawidłowy login lub hasło';

        return new JsonResponse(['error' => $message], Response::HTTP_UNAUTHORIZED);
    }
}
