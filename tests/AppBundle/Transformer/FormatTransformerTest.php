<?php

namespace Tests\AppBundle\Transformer;

use AppBundle\Transformer\FormatTransformer;
use PHPUnit\Framework\TestCase;

class FormatTransformerTest extends TestCase
{
    /**
     * @var FormatTransformer
     */
    private $transformer;

    protected function setUp()
    {
        $this->transformer = new FormatTransformer();
    }

    public function testBytesToSize()
    {
        $this->assertEquals('0 B', $this->transformer->bytesToSize(0, 2));
        $this->assertEquals('1.5 MB', $this->transformer->bytesToSize(512 * 1024 + 1024 * 1024, 2));
        $this->assertEquals('512 B', $this->transformer->bytesToSize(512, 2));
        $this->assertEquals('1 KB', $this->transformer->bytesToSize(1024, 2));
        $this->assertEquals('1 MB', $this->transformer->bytesToSize(1024 * 1024, 2));
        $this->assertEquals('1 GB', $this->transformer->bytesToSize(1024 * 1024 * 1024, 2));
        $this->assertEquals('1 TB', $this->transformer->bytesToSize(1024 * 1024 * 1024 * 1024, 2));
    }
}
