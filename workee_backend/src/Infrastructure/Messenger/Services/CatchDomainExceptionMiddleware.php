<?php

namespace App\Infrastructure\Messenger\Services;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class CatchDomainExceptionMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch(HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }
}