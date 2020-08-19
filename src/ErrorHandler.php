<?php

declare(strict_types=1);

namespace webignition\ErrorHandler;

use webignition\ErrorHandler\ExceptionExaminer\AlwaysFatalExaminer;
use webignition\ErrorHandler\ExceptionExaminer\ExceptionExaminerInterface;

class ErrorHandler
{
    private ExceptionExaminerInterface $exceptionExaminer;
    private ?\ErrorException $lastError = null;

    public function __construct(?ExceptionExaminerInterface $exceptionExaminer = null)
    {
        $this->exceptionExaminer = $exceptionExaminer ?? new AlwaysFatalExaminer();
    }

    public function withExceptionExaminer(ExceptionExaminerInterface $exceptionExaminer): self
    {
        $new = clone $this;
        $new->exceptionExaminer = $exceptionExaminer;

        return $new;
    }

    public function start(): void
    {
        $this->lastError = null;

        set_error_handler(function (int $severity, string $errorMessage, ?string $file, ?int $line) {
            $file = $file ?? '';
            $line = $line ?? 0;

            $this->lastError = new \ErrorException($errorMessage, 0, $severity, $file, $line);
        });
    }

    /**
     * @throws \ErrorException
     */
    public function stop(): void
    {
        restore_error_handler();

        $lastError = $this->lastError;
        $this->lastError = null;

        if ($lastError instanceof \ErrorException) {
            if ($this->exceptionExaminer->isFatal($lastError)) {
                throw $lastError;
            }
        }
    }
}
