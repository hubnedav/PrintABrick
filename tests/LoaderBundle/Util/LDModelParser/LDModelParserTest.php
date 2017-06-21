<?php

namespace Tests\LoaderBundle\Util\LDModelParser;

use AppBundle\Exception\ErrorParsingLineException;
use AppBundle\Exception\ParseErrorException;
use LoaderBundle\Util\LDModelParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\DateTime;

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

    public function testValid()
    {
        $resource = file_get_contents(__DIR__ . '/fixtures/valid.dat');

        $array = [
            "id" => "1234",
            "name" => "Category Name",
            "category" => "Category!",
            "keywords" => [
                'keyword1', 'keyword 2', 'keyword3', 'keyword4'
            ],
            "author" => "Author [nickname]",
            "modified" => new \DateTime('2017-04-01'),
            "type" => "Part",
            'subparts' => [
                'submodel' => [
                    1 => 2,
                    16 => 1
                ]
            ],
            "parent" => null,
            "license" => "Redistributable under CCAL version 2.0",
        ];

        $this->assertEquals($array, $this->parser->parse($resource));
    }


    /**
     * @expectedException LoaderBundle\Exception\ErrorParsingLineException
     */
    public function testInvalid()
    {
        $resource = file_get_contents(__DIR__ . '/fixtures/invalid.dat');

        $this->parser->parse($resource);
    }

    public function testStickers() {
        $resource = file_get_contents(__DIR__ . '/fixtures/stickers.txt');

        foreach (preg_split('/^---DAT/m', $resource) as $dat) {
            $this->assertEquals('Sticker', $this->parser->parse($dat)['type']);
        }
    }

    public function testAlias()
    {
        $resource = file_get_contents(__DIR__ . '/fixtures/alias.txt');

        foreach (preg_split('/^---DAT/m', $resource) as $dat) {
            $this->assertEquals('parent', $this->parser->parse($dat)['parent']);
        }
    }
}