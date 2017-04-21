<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRelationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:relations')
            ->setDescription('Loads relations between LDraw models and Rebrickable parts.')
            ->setHelp('This command allows you to load relation between models and parts into database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $relationLoader = $this->getContainer()->get('service.loader.relation');
        $relationLoader->setOutput($output);


        $output->writeln([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            "<fg=cyan>Loading relations between parts and models...</>",
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        $relationLoader->loadAll();

        $output->writeln(['<info>Done!</info>']);
    }
}
