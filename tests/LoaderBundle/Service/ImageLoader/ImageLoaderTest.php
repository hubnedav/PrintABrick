<?php

namespace Tests\LoaderBundle\Service;

use League\Flysystem\File;
use LoaderBundle\Service\ImageLoader;
use LoaderBundle\Service\Stl\StlRendererService;
use Monolog\Logger;
use Symfony\Component\Console\Output\NullOutput;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class ImageLoaderTest extends BaseTest
{
    /**
     * @var ImageLoader
     */
    private $imageLoader;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $file = $this->createMock(File::class);
        $file->method('getPath')->willReturn('path');

        $stlRenderer = $this->createMock(StlRendererService::class);
        $stlRenderer->method('render')->willReturn('image');

        $this->imageLoader = new ImageLoader($this->em, $this->get('monolog.logger.event'), $this->filesystem, __DIR__.'/fixtures/', $stlRenderer);
        $this->imageLoader->setOutput(new NullOutput());
    }

    public function testLoadFromRebrickable()
    {
        $this->imageLoader->loadColorFromRebrickable(-1);

        $this->assertCount(8, $this->filesystem->listContents('images/-1/'));
    }

    /**
     * @expectedException \LoaderBundle\Exception\FileException
     */
    public function testLoadCorrupted()
    {
        $this->imageLoader->loadColorFromRebrickable(5);
    }

    public function testLoadMissingImages()
    {
        $stlRenderer = $this->createMock(StlRendererService::class);
        $stlRenderer->method('render')->willReturn('image');

        $stlRenderer->expects($this->exactly(4))->method('render');

        $this->imageLoader = new ImageLoader($this->em, $this->get('monolog.logger.event'), $this->filesystem, __DIR__.'/fixtures/', $stlRenderer);
        $this->imageLoader->setOutput(new NullOutput());

        $this->imageLoader->loadMissingModelImages();
    }
}
