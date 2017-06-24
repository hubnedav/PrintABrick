<?php

namespace Tests\LoaderBundle\Command;


use LoaderBundle\Command\LoadLdrawCommand;
use LoaderBundle\Service\ModelLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class loadLdrawCommandTest extends KernelTestCase
{
    public function testMissingArgument()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $modelLoader = $this->createMock(ModelLoader::class);

        $application->add(new LoadLdrawCommand(null,$modelLoader));

        $command = $application->find('app:load:ldraw');

        $tester = new CommandTester($command);
        $tester->execute(
            ['--ldraw' => 'path2']
        );

        $this->assertEquals('Either the --all or --file option is required'."\n",$tester->getDisplay());
    }

    public function testDownload()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $modelLoader = $this->createMock(ModelLoader::class);
        $modelLoader->expects($this->once())->method('downloadLibrary');

        $application->add(new LoadLdrawCommand(null , $modelLoader));

        $command = $application->find('app:load:ldraw');

        $tester = new CommandTester($command);
        $tester->execute([
            '--all' => true
        ]);
    }

    public function testLoadAll()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $modelLoader = $this->createMock(ModelLoader::class);
        $modelLoader->expects($this->once())->method('loadAll');

        $application->add(new LoadLdrawCommand(null,$modelLoader));

        $command = $application->find('app:load:ldraw');

        $tester = new CommandTester($command);
        $tester->execute(
           ['--ldraw' => 'path', '--all' => true]
        );
    }

    public function testLoadFile()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $modelLoader = $this->createMock(ModelLoader::class);
        $modelLoader->expects($this->once())->method('loadOne');

        $application->add(new LoadLdrawCommand(null,$modelLoader));

        $command = $application->find('app:load:ldraw');

        $tester = new CommandTester($command);
        $tester->execute(
            ['--ldraw' => 'path', '--file' => __DIR__.'/fixtures/file.dat']
        );
    }

    public function testFileNotFound()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $modelLoader = $this->createMock(ModelLoader::class);

        $application->add(new LoadLdrawCommand(null,$modelLoader));

        $command = $application->find('app:load:ldraw');

        $file = __DIR__.'/fixtures/.dat';

        $tester = new CommandTester($command);
        $tester->execute(
            ['--ldraw' => 'path', '--file' => $file]
        );

        $this->assertEquals('File '. $file.' not found'."\n",$tester->getDisplay());
    }
}