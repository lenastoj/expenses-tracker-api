<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LoginValidator
{
    private ValidatorInterface $validator;

//    private UserRepository $userRepository;


    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
//        $this->userRepository = $userRepository;
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
            'password' => [
                new Assert\NotBlank([
                    'message' => 'Password is required.',
                ]),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

//        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
//
//        if (!$user || $user->getPassword() !== $data['password']) {
//            $errors['password'] = ['message' => 'Invalid credentials'];
//        }

        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $fieldName = preg_replace('/[\[\]]/', '', $propertyPath);
            $errors[$fieldName][] = $violation->getMessage();
        }
        return $errors;
    }
}
