<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ValidationProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationProviderTest extends TestCase
{
    public function testReturnsFormattedErrors(): void
    {
        $validatorMock = $this->createMock(ValidatorInterface::class);

        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                'Email jest wymagany.',
                null,
                [],
                '',
                'email',
                ''
            ),
            new ConstraintViolation(
                'min. 6 znaków (w tym min. cyfra, jedna duża i mała litera)',
                null,
                [],
                '',
                'password',
                ''
            ),
        ]);

        $validatorMock->method('validate')->willReturn($violations);

        $validationProvider = new ValidationProvider($validatorMock);
        $errors = $validationProvider->getErrors(new \stdClass());

        $this->assertCount(2, $errors);
        $this->assertSame('/data/attributes/email', $errors[0]['source']['pointer']);
        $this->assertSame('Email jest wymagany.', $errors[0]['detail']);
        $this->assertSame('Validation Error', $errors[0]['title']);
        $this->assertSame('/data/attributes/password', $errors[1]['source']['pointer']);
        $this->assertSame('min. 6 znaków (w tym min. cyfra, jedna duża i mała litera)', $errors[1]['detail']);
        $this->assertSame('Validation Error', $errors[1]['title']);
    }

    public function testReturnsEmptyArrayForNoErrors(): void
    {
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $validatorMock->method('validate')->willReturn(new ConstraintViolationList());

        $validationProvider = new ValidationProvider($validatorMock);
        $errors = $validationProvider->getErrors(new \stdClass());

        $this->assertEmpty($errors);
    }
}
