<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable();
        if($e instanceof ApiException){
            $apiException = $e->getApiDto();
            $response = new JsonResponse(
                $apiException->toArray(),
                $apiException->getStatusCode(),
        );
            $response->headers->set('Content-Type', 'application/problem+json');
            $event->setResponse($response);
        }

    }
}