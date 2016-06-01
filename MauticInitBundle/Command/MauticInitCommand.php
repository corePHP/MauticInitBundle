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
 * CLI Command to initialize Mautic.
 */
class MauticInitCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('corephp:mautic:init')
            ->setDescription('Initializes Mautic with an Admin user.')
            ->setDefinition(array(
                new InputArgument('fname', InputArgument::REQUIRED, 'First Name'),
				new InputArgument('lname', InputArgument::REQUIRED, 'Last Name'),
				new InputArgument('email', InputArgument::REQUIRED, 'Email Address'),
				new InputArgument('username', InputArgument::REQUIRED, 'The Username'),
				new InputArgument('password', InputArgument::REQUIRED, 'Password'),
				new InputArgument('masteruser', InputArgument::REQUIRED, 'Master Username'),
				new InputArgument('masterpass', InputArgument::REQUIRED, 'Master Password')
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command initializes Mautic with a single manager account and a master admin account.
				
If Mautic has been previously setup through the install page, this command will remove all data and re-install the database.  
	
<comment>Do not run this command if you have already setup Mautic.</comment>

<info>php %command.full_name%</info>
				
The interactive shell will ask you for the following: first name, last name, email, username, and password.

You can optionally provide the required fields as arguments like so

<info>php %command.full_name% {first_name} {last_name} {email} {username} {password} {masteruser} {masterpass}</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$options = $input->getOptions();

        $env = $options['env'];
		
		// Because we can't pass information to Fixtures from here, lets just declare some constants like a lazy programmer.
		define('MAUTICINIT_FNAME', $input->getArgument('fname'));
		define('MAUTICINIT_LNAME', $input->getArgument('lname'));
		define('MAUTICINIT_EMAIL', $input->getArgument('email'));
		define('MAUTICINIT_USERNAME', $input->getArgument('username'));
		define('MAUTICINIT_PASSWORD', $input->getArgument('password'));
		define('MAUTICINIT_MASTERUSER', $input->getArgument('masteruser'));
		define('MAUTICINIT_MASTERPASS', $input->getArgument('masterpass'));

        // Drop any old data.
        $command = $this->getApplication()->find('doctrine:schema:drop');
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:drop',
            '--force' => true,
            '--env'   => $env,
            '--quiet'  => true
        ));
        $returnCode = $command->run($input, $output);

        if ($returnCode !== 0) {
            return $returnCode;
        }

        // Create the new database.
        $command = $this->getApplication()->find('doctrine:schema:create');
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:create',
            '--env'   => $env,
            '--quiet'  => true
        ));
        $returnCode = $command->run($input, $output);
        if ($returnCode !== 0) {
            return $returnCode;
        }

        // Now setup our admin account.
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $args = array(
            '--append' => true,
            'command'  => 'doctrine:fixtures:load',
            '--env'    => $env,
            '--quiet'  => true
        );

        $fixtures = $this->getFixtures();
        foreach ($fixtures as $fixture) {
            $args['--fixtures'][] = $fixture;
        }
        $input = new ArrayInput($args);
        $returnCode = $command->run($input, $output);
        if ($returnCode !== 0) {
            return $returnCode;
        }

        $output->writeln("Complete!");

        return 0;
    }

    public function getFixtures()
    {
        return array(
			__DIR__ .'/../../../app/bundles/InstallBundle/InstallFixtures/ORM',
			__DIR__ .'/../DataFixtures/ORM'
		);
    }
}
