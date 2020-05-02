<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login()
    {
            /** @var User $user */
            $user = $this->getUser();
            return $this->json(['result' => $user->getUsername()]);

    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
    }
}
