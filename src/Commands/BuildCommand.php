<?php

namespace Staticus\Commands;

use Staticus\Staticus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->setDescription('Builds the site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Building the site...</info>');

        $startTime = microtime(true);

        $staticus = new Staticus($this->basePath);
        $staticus->build();

        $buildTime = number_format(((microtime(true) - $startTime) * 1000), 2);

        $output->writeln("\n<info>Site built in {$buildTime}ms</info>");

        return Command::SUCCESS;
    }
}