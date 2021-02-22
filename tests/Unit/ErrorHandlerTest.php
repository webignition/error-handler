<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use webignition\ErrorHandler\ErrorHandler;
use webignition\ErrorHandler\Tests\Services\AlwaysRecoverableExaminer;
use webignition\ObjectReflector\ObjectReflector;

class ErrorHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testStartStop(): void
    {
        $errorHandler = new ErrorHandler();

        self::assertNull(
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );

        $errorHandler->start();
        trigger_error('error message content');

        $expectedErrorException = new \ErrorException(
            'error message content',
            0,
            E_USER_NOTICE
        );

        self::assertEquals(
            $expectedErrorException,
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );

        $this->expectExceptionObject($expectedErrorException);
        $errorHandler->stop();

        self::assertNull(
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );
    }

    public function testStartStopWithNonFatalExaminer(): void
    {
        $errorHandler = new ErrorHandler();
        $errorHandler = $errorHandler->withExceptionExaminer(new AlwaysRecoverableExaminer());

        self::assertNull(
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );

        $errorHandler->start();
        trigger_error('error message content');

        $expectedErrorException = new \ErrorException(
            'error message content',
            0,
            E_USER_NOTICE
        );

        self::assertEquals(
            $expectedErrorException,
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );

        $errorHandler->stop();

        self::assertNull(
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );
    }

    public function testStartStopWithLogging(): void
    {
        $triggeredErrorContent = 'error message content';
        $expectedErrorSeverity = E_USER_NOTICE;

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger
            ->shouldReceive('error')
            ->withArgs(function (
                string $errorMessage,
                array $context
            ) use (
                $triggeredErrorContent,
                $expectedErrorSeverity
            ) {
                self::assertSame($triggeredErrorContent, $errorMessage);
                self::assertSame($expectedErrorSeverity, $context['severity']);
                self::assertSame(__FILE__, $context['file']);

                return true;
            });

        $errorHandler = new ErrorHandler();
        $errorHandler = $errorHandler->withExceptionExaminer(new AlwaysRecoverableExaminer());
        $errorHandler = $errorHandler->withLogger($logger);

        self::assertNull(
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );

        $errorHandler->start();
        trigger_error('error message content');

        $expectedErrorException = new \ErrorException($triggeredErrorContent, 0, $expectedErrorSeverity);

        self::assertEquals(
            $expectedErrorException,
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );

        $errorHandler->stop();

        self::assertNull(
            ObjectReflector::getProperty($errorHandler, 'lastError')
        );
    }
}
