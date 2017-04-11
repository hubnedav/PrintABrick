<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadModelsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this
            ->setName('app:load:models')
            ->setDescription('Loads LDraw library models into database')
            ->setHelp('This command allows you to load LDraw library models into while converting .dat files to .stl')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('ldraw', InputArgument::REQUIRED, 'Path to LDraw library directory'),
                    new InputOption('images', 'i',InputOption::VALUE_NONE, 'Do you want to generate images of models?'),
                    new InputOption('all','a',InputOption::VALUE_NONE, 'Do you want to load whole LDraw libary?'),
                    new InputOption('file','f',InputOption::VALUE_REQUIRED, 'Path to DAT file that should be loaded into database')
                ])
            );
    }

    //TODO log errors
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $ldrawLoader = $this->getContainer()->get('service.loader.model');
        $ldrawLoader->setOutput($output);
        $ldrawLoader->setLDrawLibraryContext(realpath($input->getArgument('ldraw')));

        try {
            if (($ldrawPath = $input->getOption('file')) != null) {
                $ldrawLoader->loadFileContext(dirname(realpath($ldrawPath)));

                $model = $ldrawLoader->loadModel($ldrawPath);

                if($model !== null) {
                    $this->getContainer()->get('manager.ldraw.model')->getRepository()->save($model);
                }
            }
            if ($input->getOption('all')) {
               $ldrawLoader->loadAllModels();
            }
        } catch (\Exception $e) {
            printf($e->getMessage());
        }

        $this->release();
    }
}
