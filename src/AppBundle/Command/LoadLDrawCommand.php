<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class LoadLDrawCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:ldraw')
            ->setDescription('Loads LDraw library parts')
            ->setHelp("This command allows you to..")
            ->addArgument('ldraw', InputArgument::REQUIRED, 'Path to LDraw library folder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('loader.ldraw')->loadModels($input->getArgument('ldraw'));
    }
}
