<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Event\TaskEvent;
use App\Model\TaskDTO;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/api/tasks', name: 'api_task_list', methods: ['GET'])]
    public function list(): Response
    {
        return new JsonResponse(
            $this->serializer->serialize($this->taskRepository->getAll(), 'json', ['groups' => ['api']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/tasks', name: 'api_task_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $taskDTO = $this->serializer->deserialize($request->getContent(), TaskDTO::class, 'json');
        $validationErrors = $this->validator->validate($taskDTO);

        if (count($validationErrors) > 0) {
            $validationErrorMessages = [];

            foreach ($validationErrors as $validationError) {
                $validationErrorMessages[$validationError->getPropertyPath()] = $validationError->getMessage();
            }

            return new JsonResponse(['validation_errors' => $validationErrorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->eventDispatcher->dispatch(new TaskEvent($taskDTO));

        return new JsonResponse(['status' => 'success'], Response::HTTP_CREATED);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_update', methods: ['PUT'])]
    public function update(Task $task, Request $request): Response
    {
        $taskDTO = $this->serializer->deserialize($request->getContent(), TaskDTO::class, 'json');
        $validationErrors = $this->validator->validate($taskDTO);

        if (count($validationErrors) > 0) {
            $validationErrorMessages = [];

            foreach ($validationErrors as $validationError) {
                $validationErrorMessages[$validationError->getPropertyPath()] = $validationError->getMessage();
            }

            return new JsonResponse(['validation_errors' => $validationErrorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->eventDispatcher->dispatch(new TaskEvent($taskDTO, $task));

        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    public function delete(Task $task): JsonResponse
    {
        $this->taskRepository->removeWithFlush($task);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_show', methods: ['GET'])]
    public function show(Task $task): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($task, 'json', ['groups' => ['api']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
