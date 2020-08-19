<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\Tests\Services;

use webignition\ErrorHandler\ExceptionExaminer\ExceptionExaminerInterface;

class NeverFatalExaminer implements ExceptionExaminerInterface
{
    public function isFatal(\Exception $exception): bool
    {
        return false;
    }
}
