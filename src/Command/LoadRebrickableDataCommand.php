<?php

namespace App\Command;

use App\Service\Loader\RebrickableLoader;
use League\Flysystem\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadRebrickableDataCommand extends Command
{
    protected static $defaultName = 'app:load:rebrickable';

    private RebrickableLoader $rebrickableLoader;

    /**
     * LoadRebrickableDataCommand constructor.
     */
    public function __construct(RebrickableLoader $rebrickableLoader)
    {
        $this->rebrickableLoader = $rebrickableLoader;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Loads Rebrickable data about sets and parts into database.')
            ->setHelp('This command allows you to load Rebrickable CSV files containing information about sets and parts into database.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('rewrite', 'r', InputOption::VALUE_NONE, 'Truncate rebrickable tables before loading new data.'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rebrickableLoader->setOutput($io);

        try {
            $this->rebrickableLoader->loadAll($input->getOption('rewrite'));
        } catch (Exception $exception) {
            $io->error($exception->getMessage());

            return 1;
        }

        // Populate Index
        $elasticIndex = $this->getApplication()->find('fos:elastic:populate');
        $returnCode = $elasticIndex->run($input, $output);

        if ($returnCode) {
            return 1;
        }

        return 0;
    }
}
