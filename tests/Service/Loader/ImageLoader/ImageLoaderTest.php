<?php

namespace App\Tests\Service\Loader;

use App\Service\Loader\ImageLoader;
use App\Service\Loader\Stl\StlRendererService;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;
use League\Flysystem\File;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Output\NullOutput;

class ImageLoaderTest extends BaseTest
{
    /**
     * @var ImageLoader
     */
    private $imageLoader;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $file = $this->createMock(File::class);
        $file->method('getPath')->willReturn('path');

        $stlRenderer = $this->createMock(StlRendererService::class);
        $stlRenderer->method('render')->willReturn('image');

        $this->imageLoader = new ImageLoader($this->em, new NullLogger(), $this->filesystem, __DIR__.'/fixtures/', $stlRenderer);
        $this->imageLoader->setOutput(new NullOutput());
    }

    public function testLoadFromRebrickable()
    {
        $this->imageLoader->loadColorFromRebrickable(-1);

        $this->assertCount(8, $this->filesystem->listContents('images/-1/'));
    }

    /**
     * @expectedException \App\Exception\FileException
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

        $this->imageLoader = new ImageLoader($this->em, new NullLogger(), $this->filesystem, __DIR__.'/fixtures/', $stlRenderer);
        $this->imageLoader->setOutput(new NullOutput());

        $this->imageLoader->loadMissingModelImages();
    }
}
