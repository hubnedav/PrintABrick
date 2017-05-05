<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:init')
            ->setDescription('Loads relations between LDraw models and Rebrickable parts.')
            ->setHelp('This command allows you to load relation between models and parts into database.')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('ldraw', InputArgument::OPTIONAL, 'Path to LDraw library directory'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loadModelsCommand = $this->getApplication()->find('app:load:models');

        $returnCode = $loadModelsCommand->run(new ArrayInput([
            'command' => 'app:load:models',
            'ldraw' => $input->getArgument('ldraw'),
            '--all' => true,
        ]), $output);

        if ($returnCode) {
            return 1;
        }

        $loadRebrickableCommad = $this->getApplication()->find('app:load:rebrickable');
        $returnCode = $loadRebrickableCommad->run(new ArrayInput(['command' => 'app:load:rebrickable']), $output);

        if ($returnCode) {
            return 1;
        }

        $loadRelationsCommand = $this->getApplication()->find('app:load:relations');

        $returnCode = $loadRelationsCommand->run(new ArrayInput(['command' => 'app:load:relations']), $output);

        if ($returnCode) {
            return 1;
        }

        $loadImagesCommand = $this->getApplication()->find('app:load:images');
        $returnCode = $loadImagesCommand->run(new ArrayInput([
            'command' => 'app:load:images',
            '--color' => -1,
            '--rebrickable' => true,
            '--models' => true,
        ]), $output);

        return 0;
    }
}
