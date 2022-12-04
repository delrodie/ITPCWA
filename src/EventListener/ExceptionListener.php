<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class ExceptionListener
{
    public function __construct(
        private Environment $twig
    )
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable(); //dd($exception->getHeaders());

        if (!$exception instanceof NotFoundHttpException) return ;

        $content = $this->twig->render('exception/not_found.html.twig',[
            'status' => $exception->getStatusCode(),
            'message' => $exception->getMessage(),
        ]);

        $event->setResponse((new Response())->setContent($content));
    }
}