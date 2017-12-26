<?php

declare(strict_types=1);

namespace SimpleBus\Message\Bus\Middleware;

interface MessageBusMiddleware
{
    /**
     * The provided $next callable should be called whenever the next middleware should start handling the message.
     * Its only argument should be a Message object (usually the same as the originally provided message).
     *
     * @param object   $message
     * @param callable $next
     */
    public function handle($message, callable $next);
}
