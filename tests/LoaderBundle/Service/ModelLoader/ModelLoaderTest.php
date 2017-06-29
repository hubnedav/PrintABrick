<?php

namespace Tests\LoaderBundle\Service;

use AppBundle\DataFixtures\ORM\LoadColors;
use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Repository\LDraw\AliasRepository;
use AppBundle\Repository\LDraw\ModelRepository;
use League\Flysystem\File;
use LoaderBundle\Exception\Loader\MissingContextException;
use LoaderBundle\Service\ModelLoader;
use LoaderBundle\Service\Stl\StlConverterService;
use LoaderBundle\Util\RelationMapper;
use Symfony\Component\Console\Output\NullOutput;
use Tests\AppBundle\BaseTest;

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

    /** @var AliasRepository */
    private $aliasRepository;

    public function setUp()
    {
        parent::setUp();

        $this->modelRepository = $this->em->getRepository(Model::class);
        $this->aliasRepository = $this->em->getRepository(Alias::class);

        $file = $this->createMock(File::class);
        $file->method('getPath')->willReturn('path');

        $stlConverter = $this->createMock(StlConverterService::class);
        $stlConverter->method('datToStl')
            ->willReturn($file);

        $relationMapper = $this->createMock(RelationMapper::class);
        $relationMapper->method('find')
            ->will($this->returnArgument(0));

        $this->modelLoader = new ModelLoader($this->em, $this->get('monolog.logger.event'), $stlConverter, $relationMapper);
        $this->modelLoader->setOutput(new NullOutput());
        $this->setUpDb([LoadColors::class]);
    }

    public function testLoadOne()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/librarycontext/');
        $this->modelLoader->loadOne(__DIR__.'/fixtures/librarycontext/parts/3820.dat');

        /** @var Model[] $models */
        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals(3820, $models[0]->getId());
        $this->assertEquals('path', $models[0]->getPath());
    }

    /**
     * @expectedException LoaderBundle\Exception\Loader\MissingContextException
     */
    public function testFileNonExistingContext()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/nonexisting/');
    }

    public function testFileContext()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/librarycontext/');
        $this->modelLoader->loadOne(__DIR__.'/fixtures/filecontext/parts/999.dat');

        /** @var Model[] $models */
        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals(3820, $models[0]->getId());
        $this->assertEquals(2, count($models[0]->getAliases()));
    }

    public function testLoadAlias()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/librarycontext/');
        $this->modelLoader->loadOne(__DIR__.'/fixtures/filecontext/parts/999.dat');

        /** @var Model[] $models */
        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals(3820, $models[0]->getId());
        $this->assertEquals(2, count($models[0]->getAliases()));
    }

    public function testLicense()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/license/');
        $this->modelLoader->loadAll();

        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals('licensed', $models[0]->getId());
    }

    public function testLoadSubpart()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/subparts/');
        $this->modelLoader->loadOne(__DIR__.'/fixtures/subparts/parts/3815c01.dat');

        $models = $this->modelRepository->findAll();

        $this->assertEquals(4, count($models));
    }

    /**
     * @expectedException LoaderBundle\Exception\Loader\LoadingModelFailedException
     */
    public function testOneMissing()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/subparts/');
        $this->modelLoader->loadOne(__DIR__.'/fixtures/subparts/parts/381c01.dat');

        $this->modelRepository->findAll();
    }

    public function testIsIncluded()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/included/');
        $this->modelLoader->loadAll();

        $models = $this->modelRepository->findAll();
        $this->assertEmpty($models);
    }

    public function testLoadAll()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/librarycontext/');
        $this->modelLoader->loadAll();

        $models = $this->modelRepository->findAll();

        $this->assertEquals(3, count($models));
    }

    public function testUpdate()
    {
        // Load original model
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/update/version1/');
        $this->modelLoader->loadAll();

        $this->assertEquals(1, count($this->modelRepository->findAll()));
        $model = $this->modelRepository->findOneByNumber('983');
        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals('3820', $model->getId());
        $this->assertEquals(2, count($model->getAliases()));

        // Load new version
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/update/version2/');
        $this->modelLoader->setRewrite(true);
        $this->modelLoader->loadAll();

        $model = $this->modelRepository->find('3821');

        $this->assertEquals(1, count($this->modelRepository->findAll()));
        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals(3, count($model->getAliases()));
        $this->assertEquals('3821', $this->aliasRepository->find('983')->getModel()->getId());
        $this->assertEquals('3821', $this->aliasRepository->find('3820')->getModel()->getId());
        $this->assertEquals('3821', $this->aliasRepository->find('500')->getModel()->getId());
    }

    public function testUpdate2()
    {
        // Load original model
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/update2/version1/');
        $this->modelLoader->loadAll();

        // Load new version
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/update2/version2/');
        $this->modelLoader->setRewrite(false);
        $this->modelLoader->loadAll();

        /** @var Model $model */
        $model = $this->modelRepository->find('3820');
        $this->assertEquals(1, count($this->modelRepository->findAll()));
        $this->assertEquals(2009, $model->getModified()->format('Y'));

        $this->modelLoader->setRewrite(true);
        $this->modelLoader->loadAll();
        $this->assertEquals(2010, $model->getModified()->format('Y'));
    }

    public function testLoadOfPrinted()
    {
        $this->modelLoader->setLDrawLibraryContext(__DIR__.'/fixtures/printed/');
        $this->modelLoader->loadOne(__DIR__.'/fixtures/printed/parts/30367bps7.dat');

        $models = $this->modelRepository->findAll();

        $this->assertEquals(1, count($models));
        $this->assertEquals('30367b', $models[0]->getId());
    }

    public function testDownload()
    {
        $library = $this->modelLoader->downloadLibrary(__DIR__.'/fixtures/ldraw.zip');

        $this->assertDirectoryExists($library);

        $library = $this->modelLoader->downloadLibrary(__DIR__.'/fixtures/completeCA.zip');

        $this->assertDirectoryExists($library);
    }

    /**
     * @expectedException \LogicException
     */
    public function testDownloadFailed()
    {
        $library = $this->modelLoader->downloadLibrary(__DIR__.'/fixtures/nofile.zip');

        $this->assertDirectoryExists($library);
    }
}
