<?php

namespace App\Services\Excel;

class ImportResult
{
    private bool $success;
    private array $errors = [];
    private int $importedCount = 0;
    private array $rowErrors = [];

    public function __construct(bool $success = true)
    {
        $this->success = $success;
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
        $this->success = false;
    }

    public function addRowError(int $row, string $message): void
    {
        $this->rowErrors[$row] = $message;
        $this->success = false;
    }

    public function incrementImported(): void
    {
        $this->importedCount++;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'imported_count' => $this->importedCount,
            'errors' => $this->errors,
            'row_errors' => $this->rowErrors
        ];
    }
}