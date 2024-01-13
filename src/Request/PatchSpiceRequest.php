<?php

namespace App\Request;

use App\Enum\Status;
use App\Model\Spice;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PatchSpiceRequest extends BaseRequest
{
    #[NotBlank()]
    #[Callback([self::class, 'isSpiceExist'])]
    public string $id;

    #[Type('string')]
    public string $name;

    #[Choice(callback: 'availableStatuses')]
    public string $status;

    public static function isSpiceExist(?int $id, ExecutionContextInterface $context): void
    {
        if (is_null($id)) {
            // validation is covered by NotBlank()
            return;
        }

        $spice = Spice::find($id);

        if(is_null($spice)) {
            $context->buildViolation('The spice with id ' . $id . ' does not exist.')
                ->addViolation();
        }
    }

    public static function availableStatuses(): array
    {
        return array_column(Status::cases(), 'value');
    }
}