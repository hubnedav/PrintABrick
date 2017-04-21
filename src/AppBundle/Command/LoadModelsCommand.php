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
            ->setHelp('This command allows you to load LDraw library models into database while converting .dat files to .stl format.')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('ldraw', InputArgument::REQUIRED, 'Path to LDraw library directory'),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, 'Load all models from LDraw libary folder (/parts directory)'),
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'Load single modle into database'),
                    new InputOption('update', 'u', InputOption::VALUE_NONE, 'Overwrite already loaded models'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return 1;
        }

        $modelLoader = $this->getContainer()->get('service.loader.model');
        $modelLoader->setOutput($output);
        $modelLoader->setRewite($input->getOption('update'));

        $ldraw = $input->getArgument('ldraw');

        if ($ldrawPath = realpath($ldraw)) {
            $modelLoader->setLDrawLibraryContext($ldrawPath);

            if (($path = $input->getOption('file')) != null) {
                if ($file = realpath($path)) {
                    $output->writeln([
                        "Loading model: {$path}",
                    ]);

                    $modelLoader->loadOne($file);

                    $errorCount = $this->getContainer()->get('monolog.logger.loader')->countErrors();
                    $errors = $errorCount ? '<error>'.$errorCount.'</error>' : '<info>0</info>';

                    $output->writeln(['Done with "'.$errors.'" errors.']);
                } else {
                    $output->writeln("File $path not found");
                }
            }

            // Load all models inside ldraw/parts directory
            if ($input->getOption('all')) {
                $output->writeln([
                    '<fg=cyan>------------------------------------------------------------------------------</>',
                    "<fg=cyan>Loading models from LDraw library:</> <comment>{$ldrawPath}</comment>",
                    '<fg=cyan>------------------------------------------------------------------------------</>',
                ]);

                $modelLoader->loadAll();

                $errorCount = $this->getContainer()->get('monolog.logger.loader')->countErrors();
                $errors = $errorCount ? '<error>'.$errorCount.'</error>' : '<info>0</info>';

                $output->writeln(['Done with "'.$errors.'" errors.']);
            }
        } else {
            $output->writeln("<error>{$ldraw} is not a valid path!</error>");
            $this->release();
            return 1;
        }

        $this->release();
    }
}
