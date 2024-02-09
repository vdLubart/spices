<?php

namespace App\Request;

use App\Contract\Request\ValidatedRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest implements ValidatedRequest
{
    protected array $properties = [];

    protected array $validationErrors = [];

    public function __construct(protected ValidatorInterface $validator)
    {
        $this->populate();
        $this->validate();
    }

    public function validate(): bool
    {
        $errors = $this->validator->validate($this);

        $messages = ['message' => 'Validation failed', 'errors' => []];

        /** @var ConstraintViolation  */
        foreach ($errors as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        if (count($messages['errors']) > 0) {
            $this->validationErrors = $messages;

            return false;
        }

        return true;
    }


    public function errorMessages(): array
    {
        return $this->validationErrors;
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    protected function getRequestContent() {
        $content = json_decode($this->getRequest()->getContent(), true);
        if (empty($content)) {
            $content = $this->getRequest()->request->all();
            if (empty($content)) {
                $content = [];
            }
        }

        return $content;
    }

    protected function populate(): void
    {
        foreach ($this->getRequestContent() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
                $this->properties[$property] = $value;
            }
        }
    }

    public function all(): array
    {
        return $this->properties;
    }
}
