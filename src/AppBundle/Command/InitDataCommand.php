<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:init')
            ->setDescription('Loads initial data')
            ->setHelp('This command allows you to load initial data of models and sets into aplication')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('ldraw', 'l', InputOption::VALUE_OPTIONAL, 'Path to LDraw library directory'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loadModelsCommand = $this->getApplication()->find('app:load:models');

        $loadModelsInput = [
            'command' => 'app:load:models',
            '--all' => true,
        ];

        if ($ldraw = $input->getOption('ldraw')) {
            $loadModelsInput['--ldraw'] = $ldraw;
        }

        $returnCode = $loadModelsCommand->run(new ArrayInput($loadModelsInput), $output);

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
            '--missing' => true,
        ]), $output);

        if ($returnCode) {
            return 1;
        }

        $elasticIndex = $this->getApplication()->find('fos:elastic:populate');
        $returnCode = $elasticIndex->run(null, $output);

        if ($returnCode) {
            return 1;
        }

        return 0;
    }
}
