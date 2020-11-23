<?php

namespace App\Command;

use App\Service\Loader\RelationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadRelationCommand extends Command
{
    /** @var RelationLoader */
    private $relationLoader;

    /**
     * LoadRelationCommand constructor.
     */
    public function __construct(RelationLoader $relationLoader)
    {
        $this->relationLoader = $relationLoader;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:load:relations')
            ->setDescription('Loads relations between LDraw models and Rebrickable parts.')
            ->setHelp('This command allows you to load relation between models and parts into database.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('rewrite', 'r', InputOption::VALUE_NONE, 'Reload relations for all Rebrickable parts.'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->relationLoader->setOutput($io);

        $output->writeln([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            '<fg=cyan>Loading relations between parts and models...</>',
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        if ($input->getOption('rewrite')) {
            $this->relationLoader->loadAll();
        } else {
            $this->relationLoader->loadNotPaired();
        }

        $output->writeln(['<info>Done!</info>']);

        return 0;
    }
}
