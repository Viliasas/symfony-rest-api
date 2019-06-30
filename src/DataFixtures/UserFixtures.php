<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadGroups($manager);

        $this->loadUsers($manager);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadGroups(ObjectManager $manager)
    {
        $basicGroup = new Group();
        $basicGroup->setTitle('Basic');

        $manager->persist($basicGroup);

        $this->addReference('basicGroup', $basicGroup);

        $specialGroup = new Group();
        $specialGroup->setTitle('Special group');

        $manager->persist($specialGroup);

        $this->addReference('specialGroup', $specialGroup);

        $eliteGroup = new Group();
        $eliteGroup->setTitle('Elite group');

        $manager->persist($eliteGroup);

        $this->addReference('eliteGroup', $eliteGroup);
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadUsers(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->addGroup($this->getReference('eliteGroup'));

        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
        $user->addGroup($this->getReference('basicGroup'));
        $user->addGroup($this->getReference('specialGroup'));

        $manager->persist($user);
    }

}
