<?php

namespace Tests\AppBundle\Service\Stl;

use LoaderBundle\Service\Stl\StlFixerService;
use Tests\AppBundle\BaseTest;

class StlFixer extends BaseTest
{
    /** @var StlFixerService */
    protected $stlFixer;

    protected $input;

    public function setUp()
    {
        parent::setUp();

        $this->stlFixer = new StlFixerService($this->getParameter('admesh_bin'));
        $this->input = __DIR__.'/fixtures/ascii.stl';
    }

    public function testFixing()
    {
        $this->stlFixer->fix($this->input, $this->filesystem->getAdapter()->getPathPrefix().'/output.stl');

        $this->assertTrue($this->filesystem->has('output.stl'));
    }

    /**
     * @expectedException \LoaderBundle\Exception\FileNotFoundException
     */
    public function testFileNotFound()
    {
        $this->stlFixer->fix('', '');
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testCorruptProcess()
    {
        $this->stlFixer = new StlFixerService('');
        $this->stlFixer->fix($this->input);
    }
}
