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

    protected function configure()
    {
        $this->setName('corephp:mautic:initpages')
            ->setDescription('String replaces hrefs for Goodlife themes.')
            ->setDefinition(array(
                new InputArgument('info_link', InputArgument::REQUIRED, 'More Info URL'),
				new InputArgument('enroll_link', InputArgument::REQUIRED, 'Buy Now URL')
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command performs a string replace on hrefs for the sales and info links in Goodlife templates.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$options = $input->getOptions();

        $info_link = $input->getArgument('info_link');
		$enroll_link = $input->getArgument('enroll_link');
		
		if(filter_var($info_link, FILTER_VALIDATE_URL) === false)
		{
			$output->writeln(sprintf("Expected info_link to be a valid URL, got \"%s\".", $info_link));
			return 1;
		}
		
		if(filter_var($enroll_link, FILTER_VALIDATE_URL) === false)
		{
			$output->writeln(sprintf("Expected enroll_link to be a valid URL, got \"%s\".", $enroll_link));
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
				$contents = file_get_contents($realPath);
				$contents = str_replace('|ENROLL LINK|', $enroll_link, $contents);
				$contents = str_replace('|INFO LINK|', $info_link, $contents);
				file_put_contents($realPath, $contents);
			}
		}

        $output->writeln("Complete!");

        return 0;
    }
}
