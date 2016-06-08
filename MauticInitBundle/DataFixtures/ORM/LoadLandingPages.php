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
use Mautic\PageBundle\Entity\Page;
use Symfony\Component\Finder\Finder;
use Mautic\CoreBundle\Factory\MauticFactory;


class LoadLandingPages extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

	private $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
		$factory = new MauticFactory($this->container);
		$model = $factory->getModel('page.page');
		
		$finder = new Finder();
		$finder->directories()->in(__DIR__ . '/../../../../themes')->name('goodlife*');
		foreach($finder as $dir)
		{
			$page = new Page();
			$template = basename($dir->getRealPath());
			$title = ucfirst($template);
			
			$page->setTitle($title)
				->setTemplate($template);
			
			$model->saveEntity($page);
		}
	}

	public function getOrder()
	{
		return 13;
	}
}
