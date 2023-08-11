<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user', methods: ['POST'])]
    public function createUser(Request $request, UserRepository $userRepository)
    {
        try {
            $form = $this->createForm(UserType::class);
            $form->submit(json_decode($request->getContent(), true));

            if ($form->isValid()) {
                $user = $form->getData();

                $userRepository->save($user);

                return $this->json('User created successfully');
            }

            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $field = $error->getOrigin()->getName();
                $message = $error->getMessage();

                $errors[$field][] = $message;
            }
            return $this->json($errors);
        } catch (\Exception $e) {
            return $this->json([$e]);
        }
    }
}
