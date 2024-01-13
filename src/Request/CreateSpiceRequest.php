<?php

namespace App\Request;

use App\Enum\Status;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateSpiceRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    public string $name;

    #[Choice(callback: 'availableStatuses')]
    #[NotBlank()]
    public string $status;

    public static function availableStatuses(): array
    {
        return array_column(Status::cases(), 'value');
    }
}