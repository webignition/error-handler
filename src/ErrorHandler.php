<?php

declare(strict_types=1);

namespace webignition\ErrorHandler;

use Psr\Log\LoggerInterface;
use webignition\ErrorHandler\ExceptionExaminer\AlwaysFatalExaminer;
use webignition\ErrorHandler\ExceptionExaminer\ExceptionExaminerInterface;
use webignition\ErrorHandler\ExceptionLogEntryFactory\Factory;
use webignition\ErrorHandler\ExceptionLogEntryFactory\FactoryInterface;

class ErrorHandler
{
    private ExceptionExaminerInterface $exceptionExaminer;
    private ?LoggerInterface $logger = null;
    private FactoryInterface $exceptionLogEntryFactory;
    private ?\ErrorException $lastError = null;

    public function __construct()
    {
        $this->exceptionExaminer = new AlwaysFatalExaminer();
        $this->exceptionLogEntryFactory = new Factory();
    }

    public function withExceptionExaminer(ExceptionExaminerInterface $exceptionExaminer): self
    {
        $new = clone $this;
        $new->exceptionExaminer = $exceptionExaminer;

        return $new;
    }

    public function withLogger(LoggerInterface $logger): self
    {
        $new = clone $this;
        $new->logger = $logger;

        return $new;
    }

    public function withExceptionLogEntryFactory(FactoryInterface $factory): self
    {
        $new = clone $this;
        $new->exceptionLogEntryFactory = $factory;

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

            if ($this->logger instanceof LoggerInterface && false === $this->exceptionExaminer->isIgnored($lastError)) {
                $this->logger->error(
                    $this->exceptionLogEntryFactory->createMessage($lastError),
                    $this->exceptionLogEntryFactory->createContext($lastError)
                );
            }
        }
    }
}
