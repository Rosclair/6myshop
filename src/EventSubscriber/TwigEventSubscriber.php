<?php

namespace App\EventSubscriber;

use Twig\Environment;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
 
    private $repo;

    public function __construct(Environment $twig , CategoryRepository $repo)
    {
        $this->twig = $twig;
        $this->repo = $repo;
    }
    
    public function onKernelController(ControllerEvent $event): void
    {
        $this->twig->addGlobal('categories', $this->repo->findAll());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
