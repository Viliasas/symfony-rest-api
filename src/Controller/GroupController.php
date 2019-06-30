<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{

    /**
     * @Route("/groups", methods={"GET"}, name="group_list")
     */
    public function index()
    {
        return $this->json([]);
    }

}
