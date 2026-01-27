<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\UserRegisterEvent;
use App\Model\UserDTO;
use App\Service\JwtAuthService;
use App\Service\ValidationProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly ValidationProvider $validationProvider,
        private readonly JwtAuthService $jwtAuthService,
    ) {
    }

    #[Route('/api/users', name: 'api_user_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        $csrfToken = $request->headers->get('CSRF-TOKEN');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('register', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $userDTO = $this->serializer->deserialize($request->getContent(), UserDTO::class, 'json');
        $validationErrors = $this->validationProvider->getErrors($userDTO);

        if (!empty($validationErrors)) {
            return new JsonResponse(['errors' => $validationErrors], Response::HTTP_BAD_REQUEST);
        }

		$userRegisterEvent = new UserRegisterEvent($userDTO);
        $this->eventDispatcher->dispatch($userRegisterEvent);
	    $user = $userRegisterEvent->getUser();
		
		if (null === $user) {
			return new JsonResponse(['error' => 'Wystąpił błąd podczas rejestracji'], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	    
	    return $this->jwtAuthService->authenticate(
		    $user,
		    new JsonResponse(['message' => 'Rejestracja przebiegła pomyślnie'], Response::HTTP_CREATED)
	    );
    }
}
