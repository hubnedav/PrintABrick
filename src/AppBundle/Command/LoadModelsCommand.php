<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadModelsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:models')
            ->setDescription('Loads LDraw library models into database')
            ->setHelp('This command allows you to load LDraw library models into while converting .dat files to .stl')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('images', 'i'),
                    new InputOption('ldraw', 'l', InputOption::VALUE_REQUIRED),
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED),
                ))
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ldrawLoader = $this->getContainer()->get('service.loader.ldraw');
        $ldrawLoader->setOutput($output);

        //TODO log errors
        try {
            // TODO handle relative path to dir
            if (($ldrawPath = $input->getOption('file')) != null) {
                $ldrawLoader->loadFileContext($ldrawPath);

                $model = $ldrawLoader->loadModel($ldrawPath);

                if($model !== null) {
                    $this->getContainer()->get('manager.ldraw.model')->getRepository()->save($model);
                }
            } else {
                $ldrawLoader->loadAllModels();
            }
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
    }
}
