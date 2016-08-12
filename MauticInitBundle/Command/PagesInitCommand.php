<?php
/**
 * @package     MauticInit
 * @author      Jason Tolhurst
 * @link        http://jasontolhurst.com
 * @license     MIT
 */

namespace MauticPlugin\MauticInitBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * CLI Command to string replace hrefs in Goodlife themes.
 */
class PagesInitCommand extends ContainerAwareCommand
{
	
	protected $ibo_info_link;
	protected $ibo_enroll_link;
	protected $member_info_link;
	protected $member_enroll_link;

    protected function configure()
    {
        $this->setName('corephp:mautic:initpages')
            ->setDescription('String replaces hrefs for Goodlife themes.')
            ->setDefinition(array(
                new InputArgument('ibo_info_link', InputArgument::REQUIRED, 'IBO More Info URL'),
				new InputArgument('ibo_enroll_link', InputArgument::REQUIRED, 'IBO Buy Now URL'),
				new InputArgument('member_info_link', InputArgument::REQUIRED, 'Member More Info URL'),
				new InputArgument('member_enroll_link', InputArgument::REQUIRED, 'Member Buy Now URL')
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command performs a string replace on hrefs for the sales and info links in Goodlife templates.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$options = $input->getOptions();

        $this->ibo_info_link = $input->getArgument('ibo_info_link');
		$this->ibo_enroll_link = $input->getArgument('ibo_enroll_link');
		$this->member_info_link = $input->getArgument('member_info_link');
		$this->member_enroll_link = $input->getArgument('member_enroll_link');
		
		// Validate our arguments are URLs.
		if(filter_var($this->ibo_info_link, FILTER_VALIDATE_URL) === false
			|| filter_var($this->ibo_enroll_link, FILTER_VALIDATE_URL) === false
			|| filter_var($this->member_info_link, FILTER_VALIDATE_URL) === false
			|| filter_var($this->member_enroll_link, FILTER_VALIDATE_URL) === false)
		{
			$output->writeln("Invalid URLs for links.  Check arguments and try again.");
			return 1;
		}
		
		//Find our page.html.php files.
		$finder = new Finder();
		$finder->files()->in(__DIR__ . '/../../../themes/*/html')->name('page.html.php');
		
		foreach($finder as $file)
		{
			$realPath = $file->getRealpath();
			
			if(file_exists($realPath))
			{	
				list($info_link, $enroll_link) = $this->getLinks($realPath);
				
				$contents = file_get_contents($realPath);
				$contents = str_replace('|ENROLL LINK|', $enroll_link, $contents);
				$contents = str_replace('|INFO LINK|', $info_link, $contents);
				file_put_contents($realPath, $contents);
			}
		}

        $output->writeln("Complete!");

        return 0;
    }
	
	protected function getLinks($realPath)
	{
		$realPath = strtolower($realPath);
		
		if(strstr($realPath, 'ibo') !== false)
		{
			return array($this->ibo_info_link, $this->ibo_enroll_link);
		}
		else
		{
			return array($this->member_info_link, $this->member_enroll_link);
		}
	}
}
