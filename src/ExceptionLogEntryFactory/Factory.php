<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\ExceptionLogEntryFactory;

class Factory implements FactoryInterface
{
    public function createMessage(\Exception $exception): string
    {
        return $exception->getMessage();
    }

    public function createContext(\Exception $exception): array
    {
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];

        if ($exception instanceof \ErrorException) {
            $context = array_merge(
                [
                    'severity' => $exception->getSeverity(),
                ],
                $context
            );
        }

        return $context;
    }
}
