<?php

namespace FA\Tests\Model\Photo;

use FA\Model\Photo\Size;

class SizeTest extends \PHPUnit_Framework_TestCase
{
    protected $size;

    protected function setUp()
    {
        parent::setUp();
        $this->size = new Size();
    }

    protected function tearDown()
    {
        $this->size = null;
        parent::tearDown();
    }

    public function testConstructWithData()
    {
        $data = array(
            'label' => 'Large',
            'width' => '665',
            'height' => '1000',
            'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_b.jpg',
            'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/l/',
            'media' => 'photo',
        );

        $size = new Size($data);

        $this->assertEquals($data['label'], $size->getLabel());
        $this->assertEquals($data['width'], $size->getWidth());
        $this->assertEquals($data['height'], $size->getHeight());
        $this->assertEquals($data['source'], $size->getSource());
        $this->assertEquals($data['url'], $size->getUrl());
    }

    public function testFromArray()
    {
        $data = array(
            'label' => 'Large',
            'width' => '665',
            'height' => '1000',
            'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_b.jpg',
            'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/l/',
            'media' => 'photo',
        );

        $size = new Size();
        $size->fromArray($data);

        $this->assertEquals($data['label'], $size->getLabel());
        $this->assertEquals($data['width'], $size->getWidth());
        $this->assertEquals($data['height'], $size->getHeight());
        $this->assertEquals($data['source'], $size->getSource());
        $this->assertEquals($data['url'], $size->getUrl());
    }
}
