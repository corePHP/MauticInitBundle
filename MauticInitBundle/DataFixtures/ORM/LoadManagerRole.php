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
use Mautic\UserBundle\Model\RoleModel;
use Mautic\CoreBundle\Factory\MauticFactory;

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
        $rawPermissions = [
			'asset:categories' => ['full'],
			'asset:assets' => ['full'],
			'campaign:categories' => ['full'],
			'campaign:campaigns' => ['full'],
			'category:categories' => ['full'],
			'email:categories' => ['full'],
			'email:emails' => ['full'],
			'form:categories' => ['full'],
			'form:forms' => ['full'],
			'page:categories' => ['full'],
			'page:pages' => ['full'],
			'lead:leads' => ['full'],
			'lead:lists' => ['full'],
			'lead:fields' => ['full'],
			'point:categories' => ['full'],
			'point:points' => ['full'],
			'point:triggers' => ['full'],
			'report:reports' => ['full']
		];
		// Create our permissions.
		
        $role = new Role();
        $role->setName('Manager');
        $role->setDescription('Manager Access');
        $role->setIsAdmin(0);
		
		$factory = new MauticFactory($this->container);
		$model = new RoleModel($factory);
		$model->setRolePermissions($role, $rawPermissions);
		
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
