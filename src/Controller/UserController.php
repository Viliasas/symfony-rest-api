<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController {

    /**
     * @Route("/users", methods={"GET"}, name="user_list")
     */
    public function list() {
        return $this->json(["test" => "test"]);
    }

}
