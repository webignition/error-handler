<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use webignition\ErrorHandler\ErrorHandler;
use webignition\ErrorHandler\Tests\Services\NeverFatalExaminer;
use webignition\ObjectReflector\ObjectReflector;

class ErrorHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testStartStop()
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

    public function testStartStopWithNonFatalExaminer()
    {
        $errorHandler = new ErrorHandler(new NeverFatalExaminer());

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
}
