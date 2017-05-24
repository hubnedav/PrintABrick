<?php

namespace Tests\AppBundle\Service\Stl;

use AppBundle\Service\Stl\StlConverterService;
use AppBundle\Service\Stl\StlFixerService;
use AppBundle\Service\Stl\StlRendererService;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;
use Tests\AppBundle\Service\BaseTest;

class StlRendererTest extends BaseTest
{
    /** @var StlRendererService */
    protected $stlRenderer;

    public function setUp()
    {
       $this->stlRenderer = $this->get('service.stl.renderer');
    }

    public function tearDown()
    {
        $this->filesystem->delete('973c00.png');
    }

    public function testRendering()
    {
        $this->stlRenderer->render(__DIR__.'/fixtures/973c00.stl',$this->filesystem->getAdapter()->getPathPrefix());
        $this->assertTrue($this->filesystem->has('973c00.png'));
    }
}