<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadLibraryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:library')
            ->setDescription('Loads LDraw library parts')
            ->setHelp('This command allows you to..')
            ->addArgument('ldraw', InputArgument::OPTIONAL, 'Path to LDraw library folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ldrawLoader = $this->getContainer()->get('loader.ldraw');
        $ldrawLoader->setOutput($output);

        $rebrickableLoader = $this->getContainer()->get('loader.rebrickable');
        $rebrickableLoader->setOutput($output);

        try {
            $ldrawLoader->loadModels($input->getArgument('ldraw'));

            $rebrickableLoader->loadColors();

            $rebrickableLoader->loadParts();

            $rebrickableLoader->loadBuildingKits();

            $rebrickableLoader->loadPartBuildingKits();
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
    }
}
