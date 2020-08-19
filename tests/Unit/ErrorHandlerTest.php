<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use webignition\ErrorHandler\ErrorHandler;
use webignition\ObjectReflector\ObjectReflector;

class ErrorHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ErrorHandler $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->errorHandler = new ErrorHandler();
    }

    public function testStartStop()
    {
        self::assertNull(
            ObjectReflector::getProperty($this->errorHandler, 'lastError')
        );

        $this->errorHandler->start();
        trigger_error('error message content');

        $expectedErrorException = new \ErrorException(
            'error message content',
            0,
            E_USER_NOTICE
        );

        self::assertEquals(
            $expectedErrorException,
            ObjectReflector::getProperty($this->errorHandler, 'lastError')
        );

        $this->expectExceptionObject($expectedErrorException);
        $this->errorHandler->stop();

        self::assertNull(
            ObjectReflector::getProperty($this->errorHandler, 'lastError')
        );
    }
}
