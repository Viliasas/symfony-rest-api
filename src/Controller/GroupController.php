<?php

namespace App\Controller;

use App\Entity\Group;
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
    public function index()
    {
        return $this->json($this->entityManager->getRepository(Group::class)->findAll(), 200, [], ['groups' => 'api']);
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
            return $this->json([
                'success' => FALSE,
                'message' => (string)$errors
            ], 400);
        }

        $this->entityManager->persist($group);

        $this->entityManager->flush();

        return $this->json($group);
    }
}
