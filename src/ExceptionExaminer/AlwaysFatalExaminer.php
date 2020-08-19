<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\ExceptionExaminer;

class AlwaysFatalExaminer implements ExceptionExaminerInterface
{
    public function isFatal(\Exception $exception): bool
    {
        return true;
    }
}
