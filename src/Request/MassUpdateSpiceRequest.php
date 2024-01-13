<?php

namespace App\Request;

use App\Enum\Status;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class MassUpdateSpiceRequest extends BaseRequest
{
    #[NotBlank()]
    #[Type('array')]
    public array $ids;

    #[Choice(callback: 'availableStatuses')]
    public string $status;

    public static function availableStatuses(): array
    {
        return array_column(Status::cases(), 'value');
    }
}