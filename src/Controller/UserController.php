<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users", methods={"GET"}, name="user_list")
     */
    public function list()
    {
        return $this->json($this->entityManager->getRepository(User::class)
            ->findAll(), 200, [], ['groups' => ['api', 'groups']]);
    }

    /**
     * @Route("/users/{id}", methods={"POST"}, name="user_update", requirements={"id"="\d+"})
     */
    public function update(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder, int $id)
    {
        $existingUser = $this->entityManager->getRepository(User::class)->find($id);

        if (is_null($existingUser)) {
            return $this->json([
                'success' => FALSE,
                'message' => 'User not found'
            ], 404);
        }

        $currentPassword = $existingUser->getPassword();

        $serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => ['api', 'password'], 'object_to_populate' => $existingUser]);

        if (strcasecmp($currentPassword, $existingUser->getPassword()) !== 0) {
            $existingUser->setPassword($passwordEncoder->encodePassword($existingUser, $existingUser->getPassword()));
        }

        $this->entityManager->persist($existingUser);

        $this->entityManager->flush();

        return $this->json([
            'success' => TRUE,
            'message' => 'User successfully updated'
        ]);
    }

    /**
     * @Route("/users", methods={"POST"}, name="user_create")
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json([
                'success' => FALSE,
                'message' => (string)$errors
            ], 400);
        }

        $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
        $user->setRoles($user->getRoles());

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return $this->json($user);
    }

    /**
     * @Route("users/{username}", methods={"DELETE"}, name="user_delete")
     */
    public function delete(UserInterface $user, string $username)
    {
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
