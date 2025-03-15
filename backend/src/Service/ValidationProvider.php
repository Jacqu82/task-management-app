<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ValidationProvider
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function getErrors(object $dto): array
    {
        $validationErrors = $this->validator->validate($dto);
        $errors = [];

        foreach ($validationErrors as $validationError) {
            $errors[] = [
                'status' => Response::HTTP_BAD_REQUEST,
                'title' => 'Validation Error',
                'source' => ['pointer' => '/data/attributes/' . $validationError->getPropertyPath()],
                'detail' => $validationError->getMessage(),
            ];
        }

        return $errors;
    }
}
