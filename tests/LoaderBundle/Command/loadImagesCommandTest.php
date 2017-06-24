<?php

namespace Tests\LoaderBundle\Command;


use LoaderBundle\Command\LoadImagesCommand;
use LoaderBundle\Command\LoadLdrawCommand;
use LoaderBundle\Service\ImageLoader;
use LoaderBundle\Service\ModelLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class loadImagesCommandTest extends KernelTestCase
{
    public function testLoadRebrickable()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $imageLoader = $this->createMock(ImageLoader::class);
        $imageLoader->expects($this->once())->method('loadColorFromRebrickable')->with(-1);
        $imageLoader->expects($this->once())->method('loadMissingModelImages');

        $application->add(new LoadImagesCommand(null,$imageLoader));

        $command = $application->find('app:load:images');

        $tester = new CommandTester($command);
        $tester->execute(
            [
                '--rebrickable' => true,
                '--color' => -1,
                '--missing' => true
            ]
        );

    }

}