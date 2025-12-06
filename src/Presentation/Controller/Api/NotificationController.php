<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Application\Service\NoAvailableChannelException;
use App\Application\UseCase\SendNotification\SendNotificationCommand;
use App\Application\UseCase\SendNotification\SendNotificationHandler;
use App\Infrastructure\Factory\UnsupportedChannelException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications', name: 'api_notifications_')]
final class NotificationController extends AbstractController
{
    public function __construct(
        private SendNotificationHandler $handler,
    ) {
    }

    #[Route('/send', name: 'send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        try {
            // 1. Parse request body
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return $this->json(
                    ['error' => 'Invalid JSON'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // 2. Validate required fields
            if (!isset($data['userId']) || !isset($data['message'])) {
                return $this->json(
                    ['error' => 'Missing required fields: userId, message'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // 3. Create command
            $command = new SendNotificationCommand(
                userId: $data['userId'],
                message: $data['message'],
                metadata: $data['metadata'] ?? []
            );

            // 4. Execute use case
            $response = $this->handler->execute($command);

            // 5. Return response (202 Accepted for async notifications)
            return $this->json(
                $response->toArray(),
                Response::HTTP_ACCEPTED
            );
        } catch (NoAvailableChannelException $e) {
            return $this->json(
                ['error' => 'No available notification channel for user'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (UnsupportedChannelException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\RuntimeException $e) {
            // User not found, notification send failure, etc.
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => 'Internal server error: '.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
