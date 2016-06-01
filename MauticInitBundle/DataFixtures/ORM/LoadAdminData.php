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
use Mautic\UserBundle\Entity\User;

class LoadAdminData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

	private $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
		$email = null;
		$username = null;
		$password = null;
		
		if (defined('MAUTICINIT_MASTERUSER'))
		{
			$username = MAUTICINIT_MASTERUSER;
		}
		if (defined('MAUTICINIT_MASTERPASS'))
		{
			$password = MAUTICINIT_MASTERPASS;
		}
		if (defined('MAUTICINIT_EMAIL'))
		{
			$email = MAUTICINIT_EMAIL;
		}

		if (!$email || !$username || !$password)
		{
			return false;
		}

		$user = new User();
		$user->setFirstName('Master');
		$user->setLastName('Admin');
		$user->setUsername($username);
		$user->setEmail($email);
		$encoder = $this->container
			->get('security.encoder_factory')
			->getEncoder($user)
		;
		$user->setPassword($encoder->encodePassword($password, $user->getSalt()));
		$user->setRole($this->getReference('admin-role'));
		$manager->persist($user);
		$manager->flush();
	}

	public function getOrder()
	{
		return 11;
	}

}
