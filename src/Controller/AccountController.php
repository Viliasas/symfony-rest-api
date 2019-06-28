<?php

namespace App\Controller;

use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController {

    /**
     * @var \App\Service\JwtService
     */
    private $jwtService;

    public function __construct(JwtService $jwtService) {
        $this->jwtService = $jwtService;
    }

    /**
     * @Route("/account/login", methods={"POST"}, name="account_login")
     */
    public function login() {
        $user = $this->getUser();

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'token' => $this->jwtService->generateToken($user)
        ]);
    }

}
