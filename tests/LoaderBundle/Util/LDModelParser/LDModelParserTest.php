<?php

namespace Tests\LoaderBundle\Util\LDModelParser;

use LoaderBundle\Util\LDModelParser;
use PHPUnit\Framework\TestCase;

class LDModelParserTest extends TestCase
{
    /**
     * @var LDModelParser
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = new LDModelParser();
    }

    public function testParse()
    {
        $resource = file_get_contents(__DIR__.'/fixtures/valid.dat');

        $array = [
            'id' => '1234',
            'name' => 'Category Name',
            'category' => 'Category!',
            'keywords' => [
                'keyword1', 'keyword 2', 'keyword3', 'keyword4',
            ],
            'author' => 'Author [nickname]',
            'modified' => new \DateTime('2017-04-01'),
            'type' => 'Part',
            'subparts' => [
                'submodel' => [
                    1 => 2,
                    16 => 1,
                ],
            ],
            'parent' => null,
            'license' => 'Redistributable under CCAL version 2.0',
        ];

        $this->assertEquals($array, $this->parser->parse($resource));
    }

    /**
     * @expectedException \LoaderBundle\Exception\ErrorParsingLineException
     */
    public function testInvalid()
    {
        $resource = file_get_contents(__DIR__.'/fixtures/invalid.dat');

        $this->parser->parse($resource);
    }

    public function testStickers()
    {
        $resource = file_get_contents(__DIR__.'/fixtures/stickers.txt');

        foreach (preg_split('/^---DAT/m', $resource) as $dat) {
            $this->assertEquals('Sticker', $this->parser->parse($dat)['type']);
        }
    }

    public function testColor()
    {
        $resource = file_get_contents(__DIR__.'/fixtures/color.dat');

        $this->assertEquals('3705', $this->parser->parse($resource)['parent']);
    }

    public function testAlias()
    {
        $resource = file_get_contents(__DIR__.'/fixtures/alias.txt');

        foreach (preg_split('/^---DAT/m', $resource) as $dat) {
            $this->assertEquals('parent', $this->parser->parse($dat)['parent']);
        }
    }
}
