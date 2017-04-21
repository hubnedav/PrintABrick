<?php

namespace AppBundle\Command;

use League\Flysystem\Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRebrickableDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:rebrickable')
            ->setDescription('Loads Rebrickable data about sets and parts into database.')
            ->setHelp('This command allows you to load Rebrickable CSV files containing information about sets and parts into database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rebrickableLoader = $this->getContainer()->get('service.loader.rebrickable');
        $rebrickableLoader->setOutput($output);

        try {
            $rebrickableLoader->loadAll();
        } catch (Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
            return 1;
        }
    }
}
