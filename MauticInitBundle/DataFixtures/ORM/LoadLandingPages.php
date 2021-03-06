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
		$model = $this->container->get('mautic.factory')->getModel('page.page');
		$helper = $this->container->get('mautic.helper.theme');
		$templating = $this->container->get('templating');
		
		$finder = new Finder();
		$finder->directories()->in(__DIR__ . '/../../../../themes');
		foreach($finder as $dir)
		{
			$page = $model->getEntity();
			$template = basename($dir->getRealPath());
			$title = $this->getTitle($template);
			
			if($title === false)
			{
				// Unrecognized template naming convention.
				continue;
			}
			
			$logicalName = $helper->checkForTwigTemplate(':' . $template . ':page.html.php');
			
			$customHTML = $templating->render($logicalName);
			
			$page->setTitle($title)
				->setTemplate($template)
				->setCustomHtml($customHTML);
			
			$model->saveEntity($page);
		}
	}

	public function getOrder()
	{
		return 13;
	}
	
	protected function getTitle($template)
	{
		$parts = explode('_', $template);
		if(count($parts) === 2)
		{
			$title = ucfirst($parts[0]);
			return $title;
		}
		else
		{
			return false;
		}
	}
}
