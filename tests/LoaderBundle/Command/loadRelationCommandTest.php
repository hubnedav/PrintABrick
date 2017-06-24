<?php

namespace Tests\LoaderBundle\Command;


use LoaderBundle\Command\LoadLdrawCommand;
use LoaderBundle\Command\LoadRelationCommand;
use LoaderBundle\Service\ModelLoader;
use LoaderBundle\Service\RelationLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class loadRelationCommandTest extends KernelTestCase
{
    public function testLoadAll()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $relationLoader = $this->createMock(RelationLoader::class);
        $relationLoader->expects($this->once())->method('loadAll');

        $application->add(new LoadRelationCommand(null,$relationLoader));

        $command = $application->find('app:load:relations');

        $tester = new CommandTester($command);
        $tester->execute(
            ['--rewrite' => true]
        );
    }

    public function testLoadMissing()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $relationLoader = $this->createMock(RelationLoader::class);
        $relationLoader->expects($this->once())->method('loadNotPaired');

        $application->add(new LoadRelationCommand(null,$relationLoader));

        $command = $application->find('app:load:relations');

        $tester = new CommandTester($command);
        $tester->execute(
            ['--rewrite' => false]
        );
    }

}