<?php

namespace FA\Tests\Model\Photo;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;

class PhotoTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $photo = new Photo();
        $this->assertInternalType('array', $photo->getTags());
        $this->assertEmpty($photo->getTags());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $photo->getSizes());
        $this->assertTrue($photo->getSizes()->isEmpty());
    }

    public function testFromArray()
    {
        $data = array(
            'id' => 1,
            'day' => 1,
            'photo_id' => 7606616668,
            'posted' => '2013-04-29 15:31:56',
        );

        $photo = new Photo();
        $photo->fromArray($data);

        $this->assertEquals($data['id'], $photo->getId());
        $this->assertEquals($data['day'], $photo->getDay());
        $this->assertEquals($data['photo_id'], $photo->getPhotoId());
        $this->assertEquals($data['posted'], $photo->getPosted());
    }

    public function testConstructSetsPropertiesFromArray()
    {
        $data = array(
            'id' => 1,
            'day' => 1,
            'photo_id' => 7606616668,
            'posted' => '2013-04-29 15:31:56',
        );

        $photo = new Photo($data);

        $this->assertEquals($data['id'], $photo->getId());
        $this->assertEquals($data['day'], $photo->getDay());
        $this->assertEquals($data['photo_id'], $photo->getPhotoId());
        $this->assertEquals($data['posted'], $photo->getPosted());
    }
}
