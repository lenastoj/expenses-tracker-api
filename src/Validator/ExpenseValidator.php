<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ExpenseValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data): array
    {
        $constraints = new Assert\Collection([
            'date' => [
                new Assert\NotBlank([
                    'message' => 'Date cannot be blank.',
                ]),
                new Assert\Date(),
                new Assert\LessThanOrEqual('today', message: 'The date cannot be in the future')
            ],
            'time' => new Assert\Time(),
            'description' => [
                new Assert\NotBlank([
                    'message' => 'Description cannot be blank.',
                ]),
                new Assert\Length([
                    'min' => 2,
                    'max' => 500,
                    'minMessage' => 'Description cannot be less than {{ limit }} characters.',
                    'maxMessage' => 'Description cannot be longer than {{ limit }} characters.',
                ]),
            ],
            'amount' => [
                new Assert\NotBlank([
                    'message' => 'Amount cannot be blank.',
                ]),
                new Assert\Positive([
                    'message' => 'Amount must be a positive number.',
                ]),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'Amount must be a number.',
                ])
            ],
            'comment' => [
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => 'Comment cannot be longer than {{ limit }} characters.',
                ]),
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
