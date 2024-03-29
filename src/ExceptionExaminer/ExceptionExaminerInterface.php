<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\ExceptionExaminer;

interface ExceptionExaminerInterface
{
    public function isFatal(\Exception $exception): bool;

    public function isIgnored(\Exception $exception): bool;

    public function isRecoverable(\Exception $exception): bool;
}
