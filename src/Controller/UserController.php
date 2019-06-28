<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController {

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users", methods={"GET"}, name="user_list")
     */
    public function list() {
        return $this->json($this->entityManager->getRepository(User::class)
            ->findAll());
    }

    /**
     * @Route("/users", methods={"POST"}, name="user_update")
     */
    public function update(UserInterface $user) {
        return $this->json([]);
    }

    /**
     * @Route("users/{username}", methods={"DELETE"}, name="user_delete")
     */
    public function delete(UserInterface $user, string $username) {
        if (strcasecmp($user->getUsername(), $username) === 0) {
            return $this->json([
                'success' => FALSE,
                'message' => 'You can not delete yourself'
            ], 403);
        }

        $userToBeDeleted = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (is_null($userToBeDeleted)) {
            return $this->json([
                'success' => FALSE,
                'message' => 'User not found'
            ], 404);
        }

        $this->entityManager->remove($userToBeDeleted);

        $this->entityManager->flush();

        return $this->json([
            'success' => TRUE,
            'message' => 'User successfully deleted'
        ]);
    }

}
