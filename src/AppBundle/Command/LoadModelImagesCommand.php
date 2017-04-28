<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadModelImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:load:images')
            ->setDescription('Loads images of models')
            ->setHelp('This command allows you to load rendered images of models from Rebrickable or/and generate rendered images from stl files of models.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('color', 'c', InputOption::VALUE_REQUIRED, 'Color ID of images to load.'),
                    new InputOption('url', 'u', InputOption::VALUE_REQUIRED, 'Url of zip file to load'),
                    new InputOption('rebrickable', 'r', InputOption::VALUE_NONE, 'Download images from Rebicable.com'),
                    new InputOption('models', 'm', InputOption::VALUE_NONE, 'Load missing images of models'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $imageLoaderService = $this->getContainer()->get('service.loader.image');
        $imageLoaderService->setOutput($output);

        $color = $input->getOption('color');
        $url = $input->getOption('url');

        if ($color !== null && $input->getOption('rebrickable')) {
            $imageLoaderService->loadColorFromRebrickable($color, $url);
        }

        if ($input->getOption('models')) {
            $imageLoaderService->loadMissingModelImages();
        }
    }
}
