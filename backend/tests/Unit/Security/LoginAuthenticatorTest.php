<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Repository\UserRepository;
use App\Security\LoginAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginAuthenticatorTest extends TestCase
{
    private JWTEncoderInterface $jwtEncoder;
    private UserRepository $userRepository;
    private LoginAuthenticator $authenticator;

    protected function setUp(): void
    {
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->authenticator = new LoginAuthenticator($this->jwtEncoder, $this->userRepository);
    }

    public function testSupportsReturnsTrueWhenAccessTokenIsPresent(): void
    {
        $request = new Request([], [], [], ['access_token' => 'valid_token']);

        $this->assertTrue($this->authenticator->supports($request));
    }

    public function testSupportsReturnsFalseWhenAccessTokenIsNotPresent(): void
    {
        $request = new Request();

        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testAuthenticateThrowsExceptionWhenTokenIsMissing(): void
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Brak tokena');

        $request = new Request();
        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateThrowsExceptionWhenTokenIsInvalid(): void
    {
        $this->jwtEncoder->method('decode')->willThrowException(new JWTDecodeFailureException('Invalid token', 'Exception message'));

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Nieprawidłowy token');

        $request = new Request([], [], [], ['access_token' => 'invalid_token']);
        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateReturnsPassportForValidToken(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->jwtEncoder->method('decode')->willReturn(['username' => 'user@example.com']);
        $this->userRepository->method('findOneBy')->with(['email' => 'user@example.com'])->willReturn($user);

        $request = new Request([], [], [], ['access_token' => 'valid_token']);
        $passport = $this->authenticator->authenticate($request);

        $this->assertInstanceOf(Passport::class, $passport);
    }

    public function testOnAuthenticationSuccessReturnsNull(): void
    {
        $request = new Request();
        $token = $this->createMock(TokenInterface::class);

        $this->assertNull($this->authenticator->onAuthenticationSuccess($request, $token, 'main'));
    }

    public function testOnAuthenticationFailureReturnsJsonResponse(): void
    {
        $request = new Request();
        $exception = new AuthenticationException('Błąd autoryzacji');

        $response = $this->authenticator->onAuthenticationFailure($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Błąd autoryzacji']),
            $response->getContent()
        );
    }

    public function testStartReturnsJsonResponse(): void
    {
        $request = new Request();
        $exception = new AuthenticationException('Nieprawidłowy login lub hasło');

        $response = $this->authenticator->start($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Nieprawidłowy login lub hasło']),
            $response->getContent()
        );
    }
}
