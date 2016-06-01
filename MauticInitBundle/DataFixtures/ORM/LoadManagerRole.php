<?php

/**
 * @package     MauticInit
 * @author      Jason Tolhurst
 * @link        http://jasontolhurst.com
 * @license     MIT
 */

namespace MauticPlugin\MauticInitBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mautic\UserBundle\Entity\Role;

/**
 * Class RoleData
 */
class LoadManagerRole extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $role = new Role();
        $role->setName('Manager');
        $role->setDescription('Manager Access');
        $role->setIsAdmin(0);
        $manager->persist($role);
        $manager->flush();

        $this->addReference('manager-role', $role);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
