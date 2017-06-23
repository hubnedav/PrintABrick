<?php

namespace Tests\AppBundle\Service\Stl;

use LoaderBundle\Service\Stl\StlRendererService;
use Tests\AppBundle\BaseTest;

class StlRendererTest extends BaseTest
{
    /** @var StlRendererService */
    protected $stlRenderer;

    public function setUp()
    {
        parent::setUp();

        $layout = __DIR__.'/fixtures/layout.tmpl';
        $povray = $this->getParameter('povray_bin');
        $stl2pov = $this->getParameter('stl2pov_bin');

        $this->stlRenderer = new StlRendererService($layout, $povray, $stl2pov);
    }

    public function testRendering()
    {
        $this->stlRenderer->render(__DIR__.'/fixtures/973c00.stl', $this->filesystem->getAdapter()->getPathPrefix());
        $this->assertTrue($this->filesystem->has('973c00.png'));
    }

    /**
     * @expectedException \LoaderBundle\Exception\FileNotFoundException
     */
    public function testFileNotFound()
    {
        $this->stlRenderer->render('abc', $this->filesystem->getAdapter()->getPathPrefix());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testPovRayMissing()
    {
        $layout = __DIR__.'/fixtures/layout.tmpl';
        $stl2pov = $this->getParameter('stl2pov_bin');
        $this->stlRenderer = new StlRendererService($layout, '', $stl2pov);

        $this->stlRenderer->render(__DIR__.'/fixtures/973c00.stl', $this->filesystem->getAdapter()->getPathPrefix());
        $this->assertTrue($this->filesystem->has('973c00.png'));
    }
}
