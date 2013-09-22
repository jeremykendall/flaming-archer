<?php

namespace FA\Tests\Model\Photo;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;
use FA\Model\Photo\Size;

class PhotoTest extends \PHPUnit_Framework_TestCase
{
    protected $photo;

    protected function setUp()
    {
        parent::setUp();
        $this->photo = new Photo();
    }

    protected function tearDown()
    {
        $this->photo = null;
        parent::tearDown();
    }

    public function testDefaults()
    {
        $photo = new Photo();
        $this->assertInternalType('array', $photo->getTags());
        $this->assertEmpty($photo->getTags());
    }

    public function testFromArray()
    {
        $posted = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-04-29 15:31:56');
        $data = array(
            'id' => 1,
            'day' => 1,
            'photo_id' => 7606616668,
            'posted' => $posted,
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
        $posted = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-04-29 15:31:56');
        $data = array(
            'id' => 1,
            'day' => 1,
            'photo_id' => 7606616668,
            'posted' => $posted,
        );

        $photo = new Photo($data);

        $this->assertEquals($data['id'], $photo->getId());
        $this->assertEquals($data['day'], $photo->getDay());
        $this->assertEquals($data['photo_id'], $photo->getPhotoId());
        $this->assertEquals($data['posted'], $photo->getPosted());
    }

    public function testGetSetDay()
    {
        $this->assertNull($this->photo->getDay());
        $this->photo->setDay(22);
        $this->assertEquals(22, $this->photo->getDay());
    }

    public function testGetSetPosted()
    {
        $this->assertNull($this->photo->getPosted());
        $date = new \DateTime();
        $this->photo->setPosted($date);
        $this->assertSame($date, $this->photo->getPosted());
    }

    public function testGetSetTitle()
    {
        $this->assertNull($this->photo->getTitle());
        $this->photo->setTitle('OUT');
        $this->assertEquals('OUT', $this->photo->getTitle());
    }

    public function testGetSetDescription()
    {
        $this->assertNull($this->photo->getDescription());
        $this->photo->setDescription('Photo description');
        $this->assertEquals('Photo description', $this->photo->getDescription());
    }

    public function testGetSetId()
    {
        $this->assertNull($this->photo->getId());
        $this->photo->setId(322);
        $this->assertInternalType('int', $this->photo->getId());
        $this->assertEquals(322, $this->photo->getId());
    }

    public function testGetSetPhotoId()
    {
        $this->assertNull($this->photo->getPhotoId());
        $this->photo->setPhotoId(322);
        $this->assertInternalType('int', $this->photo->getPhotoId());
        $this->assertEquals(322, $this->photo->getPhotoId());
    }

    public function testGetSetTags()
    {
        $this->assertInternalType('array', $this->photo->getTags());
        $this->assertEmpty($this->photo->getTags());
        $tags = array('tag1', 'tag2', 'tag3');
        $this->photo->setTags($tags);
        $this->assertEquals($tags, $this->photo->getTags());
    }

    public function testSizes()
    {
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $this->photo->getSizes()
        );
        $this->assertTrue($this->photo->getSizes()->isEmpty());
        
        $size1 = new Size();
        $size2 = new Size();
        $sizes = new ArrayCollection();
        $sizes->set('large', $size1);
        $sizes->set('medium', $size2);

        $this->photo->setSizes($sizes);

        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $this->photo->getSizes()
        );
        $this->assertEquals(2, $this->photo->getSizes()->count());

        $this->assertSame($size1, $this->photo->getSize('large'));
        $size3 = new Size();
        $this->photo->setSize('small', $size3);
        $this->assertSame($size3, $this->photo->getSize('small'));
    }
}
