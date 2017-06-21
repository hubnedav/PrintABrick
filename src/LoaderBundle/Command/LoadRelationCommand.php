<?php

namespace LoaderBundle\Command;

use LoaderBundle\Service\RelationLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRelationCommand extends ContainerAwareCommand
{
    /** @var RelationLoader */
    private $relationLoader;

    /**
     * LoadRelationCommand constructor.
     *
     * @param $name
     * @param RelationLoader $relationLoader
     */
    public function __construct($name = null, RelationLoader $relationLoader)
    {
        $this->relationLoader = $relationLoader;

        parent::__construct($name);
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
        $this->relationLoader->setOutput($output);

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
    }
}
