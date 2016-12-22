<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRebrickableCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:loadRebrickable')
            ->setDescription('Loads Rebrickable csv data')
            ->setHelp("This command allows you to..")
            ->addArgument('pieces', InputArgument::REQUIRED, 'Path to Rebrickable pieces.csv file')
            ->addArgument('sets', InputArgument::REQUIRED, 'Path to Rebrickable sets.csv file')
            ->addArgument('set_pieces', InputArgument::REQUIRED, 'Path to Rebrickable set_pieces.csv file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modelLoader = $this->getContainer()->get('app.model_loader_service');

        printf('colors'."\n");
        $modelLoader->loadColors();
        printf('sets'."\n");
        $modelLoader->loadBuildingKits(getcwd().DIRECTORY_SEPARATOR.$input->getArgument('sets'));
        printf('pieces'."\n");
        $modelLoader->loadParts(getcwd().DIRECTORY_SEPARATOR.$input->getArgument('pieces'));
        printf('set_pieces'."\n");
        $modelLoader->loadPartBuildingKits(getcwd().DIRECTORY_SEPARATOR.$input->getArgument('set_pieces'));
    }
}