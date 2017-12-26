<?php

declare(strict_types=1);

namespace SimpleBus\Message\Tests\Logging;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use SimpleBus\Message\Logging\LoggingMiddleware;

class LoggingMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function it_logs_messages_before_and_after_handling_it(): void
    {
        $orderOfEvents = [];
        $message = $this->dummyMessage();

        $logLevel = LogLevel::DEBUG;

        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->exactly(2))
            ->method('log')
            ->will($this->returnCallback(function ($actualLevel, $logMessage, array $context) use (&$orderOfEvents, $message, $logLevel): void {
                $orderOfEvents[] = 'Logged: '.$logMessage;
                $this->assertSame(['message' => $message], $context);
                $this->assertSame($logLevel, $actualLevel);
            }));

        $middleware = new LoggingMiddleware($logger, $logLevel);

        $next = function ($actualMessage) use (&$orderOfEvents, $message): void {
            $orderOfEvents[] = 'Called next middleware';
            $this->assertSame($message, $actualMessage);
        };
        $middleware->handle($message, $next);

        $this->assertSame(
            [
                'Logged: Started handling a message',
                'Called next middleware',
                'Logged: Finished handling a message',
            ],
            $orderOfEvents
        );
    }

    private function dummyMessage()
    {
        return new \stdClass();
    }
}
