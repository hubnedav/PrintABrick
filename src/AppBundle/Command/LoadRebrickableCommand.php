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
            ->setName('app:load:rebrickable')
            ->setDescription('Loads Rebrickable csv data')
            ->setHelp('This command allows you to..')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rebrickableLoader = $this->getContainer()->get('loader.rebrickable');

        try {
            $rebrickableLoader->loadColors();

            $rebrickableLoader->loadParts($output);

            $rebrickableLoader->loadBuildingKits($output);

            $rebrickableLoader->loadPartBuildingKits($output);
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
    }
}
