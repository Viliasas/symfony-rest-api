<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Model\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * GroupController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/groups", methods={"GET"}, name="group_list")
     */
    public function list()
    {
        $groups = $this->entityManager->getRepository(Group::class)->findAll();

        return $this->json(new ApiResponse('OK', true, $groups), 200, [], ['groups' => ['api', 'users']]);
    }

    /**
     * @Route("/groups", methods={"POST"}, name="group_create")
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        /** @var Group $group */
        $group = $serializer->deserialize($request->getContent(), Group::class, 'json', ['groups' => 'api']);

        $errors = $validator->validate($group);

        if (count($errors) > 0) {
            return $this->json(new ApiResponse((string)$errors), 400);
        }

        $this->entityManager->persist($group);

        $this->entityManager->flush();

        return $this->json($group);
    }

    /**
     * @Route("/groups/{id}", methods={"DELETE"}, name="group_delete", requirements={"id"="\d+"})
     */
    public function delete(int $id)
    {
        $group = $this->entityManager->getRepository(Group::class)->find($id);

        if (is_null($group)) {
            return $this->json(new ApiResponse('Group not found'), 404);
        }

        if (count($group->getUsers()) > 0) {
            return $this->json(new ApiResponse('Group is not empty'), 400);
        }

        $this->entityManager->remove($group);

        $this->entityManager->flush();

        return $this->json(new ApiResponse('Group successfully deleted'));
    }

    /**
     * @Route("/groups/{groupId}/{userId}", methods={"POST"}, name="group_associate", requirements={"groupId"="\d+", "userId"="\d+"})
     */
    public function associate(int $groupId, int $userId)
    {
        $group = $this->entityManager->getRepository(Group::class)->find($groupId);

        if (is_null($group)) {
            return $this->json(new ApiResponse('Group not found'), 404);
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (is_null($user)) {
            return $this->json(new ApiResponse('User not found'), 404);
        }

        $group->addUser($user);

        $this->entityManager->persist($group);

        $this->entityManager->flush();

        return $this->json(new ApiResponse('User successfully added to group'));
    }

    /**
     * @Route("/groups/{groupId}/{userId}", methods={"DELETE"}, name="group_dissociate", requirements={"groupId"="\d+", "userId"="\d+"})
     */
    public function dissociate(int $groupId, int $userId)
    {
        $group = $this->entityManager->getRepository(Group::class)->find($groupId);

        if (is_null($group)) {
            return $this->json(new ApiResponse('Group not found'), 404);
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (is_null($user)) {
            return $this->json(new ApiResponse('User not found'), 404);
        }

        $group->removeUser($user);

        $this->entityManager->persist($group);

        $this->entityManager->flush();

        return $this->json(new ApiResponse('User successfully removed from group'));
    }
}
