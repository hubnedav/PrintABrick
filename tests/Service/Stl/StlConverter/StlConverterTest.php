<?php

namespace Tests\AppBundle\Service\Stl;

use AppBundle\Service\Stl\StlConverterService;
use AppBundle\Service\Stl\StlFixerService;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Tests\AppBundle\Service\BaseTest;

class StlConverterTest extends BaseTest
{
    /** @var StlConverterService */
    private $stlConverter;


    public function setUp()
    {
        $ldview =  $this->getParameter('ldview_bin');

        $stlFixer = $this->createMock(StlFixerService::class);
        $stlFixer->method('fix');

        $this->stlConverter = new StlConverterService($ldview,$this->filesystem,$stlFixer);

        $adapter = new Local(__DIR__.'/fixtures/ldraw');
        $ldrawLibraryContext = new Filesystem($adapter);
        $this->stlConverter->setLDrawLibraryContext($ldrawLibraryContext);
    }

    public function testConvertToStl()
    {
        $this->assertNotNull($this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat'));

        $this->assertTrue($this->filesystem->has('models/983.stl'));

        $this->filesystem->delete('models/983.stl');
    }

    /**
     * @expectedException AppBundle\Exception\Stl\LDLibraryMissingException
     */
    public function testLDContextMissing()
    {
        $this->stlConverter->setLDrawLibraryContext(null);
        $this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat');
    }

    public function testConvertToPng()
    {
        $this->assertNotNull($this->stlConverter->datToPng(__DIR__.'/fixtures/ldraw/parts/983.dat'));

        $this->assertTrue($this->filesystem->has('images/983.png'));

        $this->filesystem->delete('images/983.png');
    }
}