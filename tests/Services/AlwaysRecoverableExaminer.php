<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\Tests\Services;

use webignition\ErrorHandler\ExceptionExaminer\ExceptionExaminerInterface;

class AlwaysRecoverableExaminer implements ExceptionExaminerInterface
{
    public function isFatal(\Exception $exception): bool
    {
        return false;
    }

    public function isIgnored(\Exception $exception): bool
    {
        return false;
    }

    public function isRecoverable(\Exception $exception): bool
    {
        return true;
    }
}
