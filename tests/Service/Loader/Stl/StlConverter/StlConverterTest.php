<?php

namespace App\Tests\Service\Loader\Stl;

use App\Service\Loader\Stl\StlConverterService;
use App\Service\Loader\Stl\StlFixerService;
use App\Tests\BaseTest;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class StlConverterTest extends BaseTest
{
    /** @var StlConverterService */
    private $stlConverter;

    public function setUp()
    {
        parent::setUp();
        $ldview = self::$container->getParameter('ldview_bin');

        $stlFixer = $this->createMock(StlFixerService::class);
        $stlFixer->method('fix');

        $this->stlConverter = new StlConverterService($ldview, $this->filesystem, $stlFixer);
    }

    public function testConvertToStl()
    {
        $adapter = new Local(__DIR__.'/fixtures/ldraw');
        $this->stlConverter->setLDrawLibraryContext(new Filesystem($adapter));

        $this->assertNotNull($this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat'));

        // Check if stl file exists
        $this->assertTrue($this->filesystem->has('models/983.stl'));
    }

    public function testRewriteTrue()
    {
        $adapter = new Local(__DIR__.'/fixtures/ldraw');
        $ldrawLibraryContext = new Filesystem($adapter);
        $this->stlConverter->setLDrawLibraryContext($ldrawLibraryContext);

        $this->filesystem->write('models/983.stl', file_get_contents(__DIR__.'/fixtures/983.stl'));
        $this->assertTrue($this->filesystem->has('models/983.stl'));

        $this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat', true);

        $this->assertEquals(file_get_contents(__DIR__.'/fixtures/expected.stl'), $this->filesystem->read('models/983.stl'));
    }

    public function testRewriteFalse()
    {
        $adapter = new Local(__DIR__.'/fixtures/ldraw');
        $ldrawLibraryContext = new Filesystem($adapter);
        $this->stlConverter->setLDrawLibraryContext($ldrawLibraryContext);

        $this->filesystem->write('models/983.stl', file_get_contents(__DIR__.'/fixtures/983.stl'));
        $this->assertTrue($this->filesystem->has('models/983.stl'));

        $this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat');

        $this->assertEquals(file_get_contents(__DIR__.'/fixtures/983.stl'), $this->filesystem->read('models/983.stl'));
    }

    /**
     * @expectedException \App\Exception\Stl\LDLibraryMissingException
     */
    public function testLDContextMissing()
    {
        $this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat');
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testProcessFailedException()
    {
        $stlFixer = $this->createMock(StlFixerService::class);
        $stlFixer->method('fix');

        $this->stlConverter = new StlConverterService('', $this->filesystem, $stlFixer);

        $adapter = new Local(__DIR__.'/fixtures/ldraw');
        $ldrawLibraryContext = new Filesystem($adapter);
        $this->stlConverter->setLDrawLibraryContext($ldrawLibraryContext);

        $this->stlConverter->datToStl(__DIR__.'/fixtures/ldraw/parts/983.dat');
    }
}
