<?php

namespace LoaderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
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
        // Load LDraw data

        $loadLDrawCommand = $this->getApplication()->find('app:load:ldraw');
        $loadLDrawInput = [
            'command' => 'app:load:ldraw',
            '--all' => true,
        ];

        if ($ldraw = $input->getOption('ldraw')) {
            $loadLDrawInput['--ldraw'] = $ldraw;
        }

        $returnCode = $loadLDrawCommand->run(new ArrayInput($loadLDrawInput), $output);

        if ($returnCode) {
            return 1;
        }

        // Load Rebrickable data

        $loadRebrickableCommad = $this->getApplication()->find('app:load:rebrickable');
        $returnCode = $loadRebrickableCommad->run(new ArrayInput(['command' => 'app:load:rebrickable']), $output);

        if ($returnCode) {
            return 1;
        }

        // Load images

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

        return 0;
    }
}
