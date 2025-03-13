<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\UserRegisterEvent;
use App\Model\UserDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/api/users', name: 'api_user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $csrfToken = $request->headers->get('CSRF-TOKEN');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('register', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $userDTO = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');
        $validationErrors = $this->validator->validate($userDTO);

        if (count($validationErrors) > 0) {
            $validationErrorMessages = [];

            foreach ($validationErrors as $validationError) {
                $validationErrorMessages[$validationError->getPropertyPath()] = $validationError->getMessage();
            }

            return new JsonResponse(['validation_errors' => $validationErrorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->eventDispatcher->dispatch(new UserRegisterEvent($userDTO));

        return new JsonResponse(['message' => 'Rejestracja przebiegła pomyślnie'], Response::HTTP_CREATED);
    }
}
