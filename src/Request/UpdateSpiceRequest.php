<?php

namespace App\Request;

use App\Model\Spice;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UpdateSpiceRequest extends CreateSpiceRequest
{
    #[NotBlank()]
    #[Callback([self::class, 'isSpiceExist'])]
    public string $id;

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
}