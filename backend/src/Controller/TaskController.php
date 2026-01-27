<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Event\TaskEvent;
use App\Model\TaskDTO;
use App\Pagination\PaginationFactory;
use App\Repository\TaskRepository;
use App\Service\ValidationProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ValidationProvider $validationProvider,
        private readonly PaginationFactory $paginationFactory,
    ) {
    }

    #[Route('/api/tasks', name: 'api_task_list', methods: [Request::METHOD_GET])]
    public function list(): Response
    {
        $paginatedCollection = $this->paginationFactory->createCollection(
            $this->taskRepository->getByUser($this->getUser()),
            'api_task_list'
        );

        return new JsonResponse(
            $this->serializer->serialize($paginatedCollection, 'json', ['groups' => ['api']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/tasks', name: 'api_task_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $taskDTO = $this->serializer->deserialize($request->getContent(), TaskDTO::class, 'json');
        $validationMessages = $this->validationProvider->getErrors($taskDTO, ['all']);

        if (!empty($validationMessages)) {
            return new JsonResponse(['errors' => $validationMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->eventDispatcher->dispatch(new TaskEvent($taskDTO));

        return new JsonResponse(['status' => 'success'], Response::HTTP_CREATED);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_show', methods: [Request::METHOD_GET])]
    public function show(Task $task): JsonResponse
    {
        $this->denyAccessUnlessGranted('show', $task);

        return new JsonResponse(
            $this->serializer->serialize($task, 'json', ['groups' => ['api']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/tasks/{id}', name: 'api_task_update', methods: [Request::METHOD_PUT])]
    public function update(Task $task, Request $request): Response
    {
        $this->denyAccessUnlessGranted('update', $task);

        $taskDTO = $this->serializer->deserialize($request->getContent(), TaskDTO::class, 'json');
        $validationMessages = $this->validationProvider->getErrors($taskDTO, ['all']);

        if (!empty($validationMessages)) {
            return new JsonResponse(['errors' => $validationMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->eventDispatcher->dispatch(new TaskEvent($taskDTO, $task));

        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Task $task): JsonResponse
    {
        $this->denyAccessUnlessGranted('delete', $task);

        $this->taskRepository->removeWithFlush($task);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
    
    #[Route('/api/tasks/{id}/status', name: 'api_task_change_status', methods: [Request::METHOD_PATCH])]
    public function changeStatus(Task $task, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('update_status', $task);
        
        $taskDTO = $this->serializer->deserialize($request->getContent(), TaskDTO::class, 'json');
        $validationMessages = $this->validationProvider->getErrors($taskDTO, ['status_only']);
        
        if (!empty($validationMessages)) {
            return new JsonResponse(['errors' => $validationMessages], Response::HTTP_BAD_REQUEST);
        }
        
        $task->setStatus($taskDTO->status);
        $this->taskRepository->save($task);

        return new JsonResponse(
            [
            'id' => $task->getId(),
            'status' => $task->getStatus(),
            ]
        );
    }
    
    #[Route('/api/task-statuses', name: 'api_task_statuses', methods: [Request::METHOD_GET])]
    public function statuses(): JsonResponse
    {
        return new JsonResponse(
            array_map(
                static fn(TaskStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
                ],
                TaskStatus::cases()
            )
        );
    }
}
