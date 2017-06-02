<?php

namespace AppBundle\Command;

use AppBundle\Service\Loader\RelationLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRelationCommand extends ContainerAwareCommand
{
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
        /** @var RelationLoader $relationLoader */
        $relationLoader = $this->getContainer()->get('service.loader.relation');
        $relationLoader->setOutput($output);

        $output->writeln([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            '<fg=cyan>Loading relations between parts and models...</>',
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        if ($input->getOption('rewrite')) {
            $relationLoader->loadAll();
        } else {
            $relationLoader->loadNotPaired();
        }

        $output->writeln(['<info>Done!</info>']);
    }
}
