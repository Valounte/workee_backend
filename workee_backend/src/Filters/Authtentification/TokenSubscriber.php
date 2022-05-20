<?php

namespace App\Filters\Authentification;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Infrastructure\Services\TokenService;
use Exception;

class TokenSubscriber implements EventSubscriberInterface
{
    
    public function __construct(
        private TokenService $tokenService
        )
    {   
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof ITokenAuthenticatedController) 
        {  
            try 
            {
                $this ->tokenService->decode($event->$_REQUEST);
            }    
            catch (Exception $e) 
            {
                throw new AccessDeniedHttpException('This action needs a valid token!'); 
                return $this->jsonResponseService->errorJsonResponse('Unauthorized');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
