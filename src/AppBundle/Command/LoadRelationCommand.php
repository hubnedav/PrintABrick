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
            ->setDescription('Loads relations between LDraw models and Rebrickable parts')
            ->setHelp('This command allows you to..');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $relationLoader = $this->getContainer()->get('service.loader.relation');
        $relationLoader->setOutput($output);

        //TODO log errors
        $relationLoader->loadNotPaired();
    }
}
