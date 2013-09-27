<?php

namespace FA\Tests\Model\Photo;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;
use FA\Model\Photo\Size;

class PhotoTest extends \PHPUnit_Framework_TestCase
{
    protected $data;
    protected $photo;

    protected function setUp()
    {
        parent::setUp();

        $posted = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-09-22 15:31:56');

        $this->data = array(
            'id' => 1,
            'photoId' => 9881096656,
            'day' => 1,
            'posted' => $posted,
        );

        $this->photo = new Photo($this->data);
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
        $this->assertEquals('Untitled', $photo->getTitle());
    }

    public function testSerializeUnserialize()
    {
        $serialized = serialize($this->photo);
        $unserialized = unserialize($serialized);

        $this->assertEquals($this->photo, $unserialized);
        $this->assertNotSame($this->photo, $unserialized);
    }

    public function testFromArray()
    {
        $photo = new Photo();
        $photo->fromArray($this->data);

        $this->assertEquals($this->data['id'], $photo->getId());
        $this->assertEquals($this->data['day'], $photo->getDay());
        $this->assertEquals($this->data['photoId'], $photo->getPhotoId());
        $this->assertEquals($this->data['posted'], $photo->getPosted());
    }

    public function testToArray()
    {
        $array = $this->photo->toArray();

        $this->data['title'] = 'Untitled';
        $this->data['description'] = null;
        $this->data['owner'] = null;
        $this->data['tags'] = array();
        $this->data['sizes'] = new ArrayCollection();

        $this->assertEquals($this->data, $array);
    }

    public function testConstructSetsPropertiesFromArray()
    {
        $photo = new Photo($this->data);

        $this->assertEquals($this->data['id'], $photo->getId());
        $this->assertEquals($this->data['day'], $photo->getDay());
        $this->assertEquals($this->data['photoId'], $photo->getPhotoId());
        $this->assertEquals($this->data['posted'], $photo->getPosted());
    }

    public function testGetSetPosted()
    {
        $photo = new Photo();
        $this->assertNull($photo->getPosted());
        $date = new \DateTime();
        $photo->setPosted($date);
        $this->assertSame($date, $photo->getPosted());
    }

    public function testGetSetTitle()
    {
        $this->assertEquals('Untitled', $this->photo->getTitle());
        $this->photo->setTitle('OUT');
        $this->assertEquals('OUT', $this->photo->getTitle());

        // Setting title to empty string will then return 'Untitled'
        $this->photo->setTitle('');
        $this->assertEquals('Untitled', $this->photo->getTitle());
    }

    public function testGetSetDescription()
    {
        $this->assertNull($this->photo->getDescription());
        $this->photo->setDescription('Photo description');
        $this->assertEquals('Photo description', $this->photo->getDescription());
    }

    public function testGetSetId()
    {
        $photo = new Photo();
        $this->assertNull($photo->getId());
        $photo->setId(322);
        $this->assertInternalType('int', $photo->getId());
        $this->assertEquals(322, $photo->getId());
    }

    public function testGetSetPhotoId()
    {
        $photo = new Photo();
        $this->assertNull($photo->getPhotoId());
        $photo->setPhotoId(322);
        $this->assertInternalType('int', $photo->getPhotoId());
        $this->assertEquals(322, $photo->getPhotoId());
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

    public function testGetFeatureSize()
    {
        $med800 = new Size(array('label' => 'Medium 800'));
        $large1024 = new Size(array('label' => 'Large'));

        $this->photo->setSize('Medium 800', $med800);

        $this->assertEquals($med800, $this->photo->getFeatureSize());

        $this->photo->setSize('Large', $large1024);

        $this->assertEquals($large1024, $this->photo->getFeatureSize());
    }
}
