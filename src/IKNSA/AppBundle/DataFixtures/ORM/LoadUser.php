<?php

namespace IKNSA\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUser extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->getContainer = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->getContainer->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('user');
        $user->setEmail('user@iknsa.com');
        $user->setPlainPassword('user');
        $user->setEnabled(true);
        $user->setLastLogin(new \Datetime('NOW'));
        $user->setRoles(array('ROLE_USER'));
        $manager->persist($user);

        $admin = $userManager->createUser();
        $admin->setUsername('admin');
        $admin->setEmail('admin@iknsa.com');
        $admin->setPlainPassword('admin');
        $admin->setEnabled(true);
        $admin->setLastLogin(new \Datetime('NOW'));
        $admin->setRoles(array('ROLE_ADMIN'));
        $manager->persist($admin);

        $admin = $userManager->createUser();
        $admin->setUsername('contributor');
        $admin->setEmail('contributor@iknsa.com');
        $admin->setPlainPassword('contributor');
        $admin->setEnabled(true);
        $admin->setLastLogin(new \Datetime('NOW'));
        $admin->setRoles(array('ROLE_CONTRIBUTOR'));
        $manager->persist($admin);

        $admin = $userManager->createUser();
        $admin->setUsername('commentator');
        $admin->setEmail('commentator@iknsa.com');
        $admin->setPlainPassword('commentator');
        $admin->setEnabled(true);
        $admin->setLastLogin(new \Datetime('NOW'));
        $admin->setRoles(array('ROLE_COMMENTATOR'));
        $manager->persist($admin);

        $manager->flush();
    }
}
