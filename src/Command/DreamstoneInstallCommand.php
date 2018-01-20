<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Dreamstone installation command
 *
 * @author David Lima
 * @package App\Command
 */
class DreamstoneInstallCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('dreamstone:install')
            ->setDescription('Dreamstone initial configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setDecorated(true);

        $output->writeln('Loading environment variables');
        $dotEnvFile = __DIR__ . '/../../.env';

        if (! file_exists($dotEnvFile)) {
            throw new \RuntimeException('.env file do not exists. Please copy .env.dist file to .env and set your environment variables');
        }

        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');

        $output->writeln('Testing database configuration...');
        if (! getenv('DATABASE_URL')) {
            throw new \RuntimeException('DATABASE_URL environment variable not found. Did you installed Doctrine?');
        }

        try {
            $this->getContainer()->get('doctrine')->getConnection()->connect();
            $output->writeln('<info>Connected!</info>');
        } catch (\Exception $e) {
            $output->writeln([
                '<error>Unable to connect to database</error>',
                "<error>{$e->getMessage()}</error>",
                "<error>Check if your DATABASE_URL is correct:</error>",
                "<error>" . getenv('DATABASE_URL') . "</error>"
            ]);

            exit();
        }

        $this->runSchemaUpdate($output);
        $this->runFixturesLoad($output);
        $this->createAssetsDirectories($output);
    }

    /**
     * Create/update database schema
     *
     * @param OutputInterface $output
     */
    private function runSchemaUpdate(OutputInterface $output)
    {
        $output->writeln('Updating database schema...');
        $command = $this->getApplication()->find('doctrine:schema:update');

        $arguments = array(
            'command' => 'doctrine:schema:update',
            '--force'  => true,
        );

        $arguments = new ArrayInput($arguments);
        try {
            $command->run($arguments, $output);
            $output->writeln('<info>Done!</info>');
        } catch (\Exception $e) {
            $output->writeln([
                '<error>Unable to update database schema</error>',
                "<error>{$e->getMessage()}</error>"
            ]);
        }
    }

    /**
     * Insert all default data (fixtures) on database
     *
     * @param OutputInterface $output
     */
    private function runFixturesLoad(OutputInterface $output)
    {
        $output->writeln('Loading database initial data...');
        $command = $this->getApplication()->find('doctrine:fixtures:load');

        $arguments = array(
            'command' => 'doctrine:fixtures:load'
        );

        $arguments = new ArrayInput($arguments);
        try {
            $command->run($arguments, $output);
            $output->writeln('<info>Done!</info>');
        } catch (\Exception $e) {
            $output->writeln([
                '<error>Unable to load database initial data</error>',
                "<error>{$e->getMessage()}</error>"
            ]);
        }
    }

    /**
     * Create assets directories for system use
     *
     * @param OutputInterface $output
     */
    private function createAssetsDirectories(OutputInterface $output)
    {
        $directories = [
            'uploads',
            'uploads/pages',
            'uploads/posts',
            'uploads/usr',
            'uploads/usr-thumbs'
        ];

        foreach ($directories as $dir) {
            $fullDir = __DIR__ . "/../../public/{$dir}";
            $realPath = basename($fullDir);

            if (! is_dir($fullDir)) {
                mkdir($fullDir, 0775);
                $output->writeln("Directory {$realPath} successfully created");
            } elseif (is_dir($fullDir) && ! is_writable($fullDir)) {
                $output->writeln("Directory {$realPath} already exists but is not writable");
                chmod($fullDir, 0775);
                $output->writeln("Permissions updated for {$realPath}");
            } else {
                $output->writeln("Directory {$realPath} already exists.");
            }
        }
    }
}
