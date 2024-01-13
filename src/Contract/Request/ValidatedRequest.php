<?php

namespace App\Contract\Request;

interface ValidatedRequest
{
    public function validate(): bool;

    public function errorMessages(): array;
}