<?php

namespace Staticus\Commands;

use Staticus\Staticus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'build';

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @param string $basePath
     * @return void
     */
    public function __construct($basePath)
    {
        parent::__construct();
        $this->basePath = $basePath;
    }

    protected function configure()
    {
        $this
            ->setDescription('Builds the site')
            ->addArgument('environment', InputArgument::OPTIONAL, 'Optional environment', 'local')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Optional output directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Building the site...</info>');

        $startTime = microtime(true);

        $environment = $input->getArgument('environment');
        $outputDir   = $input->getOption('output');

        if ($outputDir && empty(realpath($outputDir))) {
            $output->writeln('<error>Output directory does not exist</error>');

            return Command::FAILURE;
        }

        $output->writeln("Environment: {$environment}");
        $output->writeln('Output directory: ' . ($outputDir ?: 'dist'));

        $staticus = new Staticus($this->basePath, $environment, $outputDir);
        $staticus->build();

        $buildTime = number_format(((microtime(true) - $startTime) * 1000), 2);

        $output->writeln("<info>Site built in {$buildTime}ms</info>");

        return Command::SUCCESS;
    }
}
