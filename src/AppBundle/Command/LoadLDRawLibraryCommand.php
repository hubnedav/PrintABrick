<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadLDRawLibraryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:ldraw')
            ->setDescription('Loads LDraw library parts')
            ->setHelp('This command allows you to..')
            ->addArgument('ldraw_path', InputArgument::OPTIONAL, 'Path to LDraw library folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ldrawLoader = $this->getContainer()->get('service.loader.ldraw');
        $ldrawLoader->setOutput($output);

        //TODO log errors

        try {
            // TODO handle relative path to dir
            if (($ldrawPath = $input->getArgument('ldraw_path')) == null) {
                $ldrawPath = $ldrawLoader->downloadLibrary();
            }

            $ldrawLoader->loadData($ldrawPath);
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
    }
}
