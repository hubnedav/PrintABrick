<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRebrickableCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:loadRebrickable')
            ->setDescription('Loads Rebrickable csv data')
            ->setHelp('This command allows you to..')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modelLoader = $this->getContainer()->get('app.model_loader_service');

        try {
            $modelLoader->loadColors();

            $modelLoader->loadParts($output);

            $modelLoader->loadBuildingKits($output);

            $modelLoader->loadPartBuildingKits($output);
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
    }
}
