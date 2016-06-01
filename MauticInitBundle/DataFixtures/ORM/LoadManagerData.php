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

class LoadManagerData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

	private $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
		$fname = null;
		$lname = null;
		$email = null;
		$username = null;
		$password = null;

		if (defined('MAUTICINIT_FNAME'))
		{
			$fname = MAUTICINIT_FNAME;
		}
		if (defined('MAUTICINIT_LNAME'))
		{
			$lname = MAUTICINIT_LNAME;
		}
		if (defined('MAUTICINIT_EMAIL'))
		{
			$email = MAUTICINIT_EMAIL;
		}
		if (defined('MAUTICINIT_USERNAME'))
		{
			$username = MAUTICINIT_USERNAME;
		}
		if (defined('MAUTICINIT_PASSWORD'))
		{
			$password = MAUTICINIT_PASSWORD;
		}

		if (!$fname || !$lname || !$email || !$username || !$password)
		{
			return false;
		}

		$user = new User();
		$user->setFirstName($fname);
		$user->setLastName($lname);
		$user->setUsername($username);
		$user->setEmail($email);
		$encoder = $this->container
			->get('security.encoder_factory')
			->getEncoder($user)
		;
		$user->setPassword($encoder->encodePassword($password, $user->getSalt()));
		$user->setRole($this->getReference('manager-role'));
		$manager->persist($user);
		$manager->flush();
	}

	public function getOrder()
	{
		return 12;
	}

}
