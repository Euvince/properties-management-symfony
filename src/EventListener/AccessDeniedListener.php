<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AccessDeniedListener
{
    function __construct(
        private readonly RouterInterface $router,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof AccessDeniedException) {
            $response = new RedirectResponse($this->router->generate('home'));
            $event->setResponse($response);
        }
    }
}
