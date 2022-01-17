<?php

declare(strict_types=1);

namespace webignition\ErrorHandler\ExceptionLogEntryFactory;

interface FactoryInterface
{
    public function createMessage(\Exception $exception): string;

    /**
     * @return array<string, mixed>
     */
    public function createContext(\Exception $exception): array;
}
