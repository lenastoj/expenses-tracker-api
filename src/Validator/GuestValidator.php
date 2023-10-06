<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GuestValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data): array
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank([
                    'message' => 'Email is required.',
                ]),
                new Assert\Email(),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $fieldName = preg_replace('/[\[\]]/', '', $propertyPath);
            $errors[$fieldName][] = $violation->getMessage();
        }

        return $errors;
    }
}
