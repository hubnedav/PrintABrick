<?php

namespace Tests\AppBundle\Service\Loader\ModelLoader;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Repository\LDraw\ModelRepository;
use AppBundle\Service\Loader\ModelLoader;
use AppBundle\Service\Stl\StlConverterService;
use AppBundle\Util\RelationMapper;
use League\Flysystem\File;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Output\NullOutput;
use Tests\AppBundle\Service\BaseTest;

class ModelLoaderTest extends BaseTest
{
    /**
     * @var ModelLoader
     */
    private $modelLoader;

    /**
     * @var ModelRepository
     */
    private $modelRepository;

    protected function setUp()
    {
        $this->modelRepository = $this->get('repository.ldraw.model');

        $file = $this->createMock(File::class);
        $file->method('getPath')->willReturn('path');

        $stlConverter = $this->createMock(StlConverterService::class);
        $stlConverter->method('datToStl')
            ->willReturn($file);

        $relationMapper = $this->createMock(RelationMapper::class);
        $relationMapper->method('find')
            ->will($this->returnArgument(0));

        $this->modelLoader = new ModelLoader($stlConverter,$relationMapper,null);
        $this->modelLoader->setArguments($this->get('doctrine.orm.entity_manager'),$this->get('monolog.logger.event'),$this->get('app.transformer.format'));
        $this->modelLoader->setOutput(new NullOutput());
        $this->setUpDb();
    }

    public function testLoadOne()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/librarycontext/');
        $this->modelLoader->loadOne(__DIR__ . '/fixtures/librarycontext/parts/3820.dat');

        /** @var Model[] $models */
        $models = $this->get('repository.ldraw.model')->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals(3820, $models[0]->getId());
        $this->assertEquals('path', $models[0]->getPath());
    }

    public function testFileContext()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/librarycontext/');
        $this->modelLoader->loadOne(__DIR__ . '/fixtures/filecontext/parts/999.dat');

        /** @var Model[] $models */
        $models = $this->get('repository.ldraw.model')->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals(3820, $models[0]->getId());
        $this->assertEquals(2,count($models[0]->getAliases()));
    }

    public function testLoadAlias()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/librarycontext/');
        $this->modelLoader->loadOne(__DIR__ . '/fixtures/filecontext/parts/999.dat');

        /** @var Model[] $models */
        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals(3820, $models[0]->getId());
        $this->assertEquals(2,count($models[0]->getAliases()));
    }

    public function testLicense()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/license/');
        $this->modelLoader->loadAll();

        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals('licensed', $models[0]->getId());
    }

    public function testIsIncluded()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/included/');
        $this->modelLoader->loadAll();

        $models = $this->modelRepository->findAll();
        $this->assertEmpty($models);
    }

    public function testLoadAll()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/librarycontext/');
        $this->modelLoader->loadAll();

        $models = $this->modelRepository->findAll();

        $this->assertEquals(3, count($models));
    }

    public function testUpdate()
    {
        // Load original model
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/update/version1/');
        $this->modelLoader->loadAll();

        $this->assertEquals(1, count($this->modelRepository->findAll()));
        $model = $this->modelRepository->findOneByNumber('983');
        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals('3820',$model->getId());
        $this->assertEquals(2, count($model->getAliases()));

        // Load new version
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/update/version2/');
        $this->modelLoader->setRewrite(true);
        $this->modelLoader->loadAll();

        $model = $this->modelRepository->find('3821');

        $this->assertEquals(1, count($this->modelRepository->findAll()));
        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals(3, count($model->getAliases()));
        $this->assertEquals('3821', $this->get('repository.ldraw.alias')->find('983')->getModel()->getId());
        $this->assertEquals('3821', $this->get('repository.ldraw.alias')->find('3820')->getModel()->getId());
        $this->assertEquals('3821', $this->get('repository.ldraw.alias')->find('500')->getModel()->getId());
    }

    public function testUpdate2()
    {
        // Load original model
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/update2/version1/');
        $this->modelLoader->loadAll();

        // Load new version
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/update2/version2/');
        $this->modelLoader->setRewrite(false);
        $this->modelLoader->loadAll();

        /** @var Model $model */
        $model = $this->modelRepository->find('3820');
        $this->assertEquals(1, count($this->modelRepository->findAll()));
        $this->assertEquals(2009,$model->getModified()->format('Y'));

        $this->modelLoader->setRewrite(true);
        $this->modelLoader->loadAll();
        $this->assertEquals(2010,$model->getModified()->format('Y'));
    }

    public function testLoadOfPrinted() {
        $this->modelLoader->setLDrawLibraryContext(__DIR__ . '/fixtures/printed/');
        $this->modelLoader->loadOne(__DIR__ . '/fixtures/printed/parts/30367bps7.dat');

        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals('30367b', $models[0]->getId());
    }
}