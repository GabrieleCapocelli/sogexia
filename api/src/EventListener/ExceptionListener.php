<?php

// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // VERB-001
        if ($exception instanceof MethodNotAllowedHttpException) {
            $response = new JsonResponse([
                'error' => [
                    'code' => 'VERB-001',
                    'title' => 'Method Not Allowed',
                    'detail' => 'Method is not allowed : use only PUT method'
                ]
            ], 405);

            $event->setResponse($response);
            return;
        }

        // SERV-001
        $response = new JsonResponse([
            'error' => [
                'code' => 'SERV-001',
                'title' => 'Internal Server Error',
                'detail' => 'The server encountered an unexpected error'
            ]
        ], 500);

        $event->setResponse($response);
    }
}
