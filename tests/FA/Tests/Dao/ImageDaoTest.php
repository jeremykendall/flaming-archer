<?php

namespace FA\Tests\Dao;

use FA\Dao\ImageDao;
use FA\Model\Photo\Photo;

/**
 * @group database
 */
class ImageDaoTest extends CommonDbTestCase
{
    /**
     * @var ImageDao
     */
    protected $dao;

    protected function setUp()
    {
        parent::setUp();
        $this->dao = new ImageDao($this->db);
    }

    protected function tearDown()
    {
        $this->dao = null;
        parent::tearDown();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\FA\Dao\ImageDao', $this->dao);
    }

    /**
     * @covers FA\Dao\ImageDao::find
     * @covers FA\Model\Photo\Photo::getPosted
     */
    public function testFind()
    {
        $result = $this->dao->find(1);
        $this->assertInstanceOf('FA\Model\Photo\Photo', $result);
        $this->assertEquals(1, $result->getDay());
        $this->assertEquals(7606616668, $result->getPhotoId());

        // PDO::FETCH_CLASS sets Photo::$posted to a string. This ensures the
        // getter is returning a DateTime instance
        $this->assertInstanceOf('DateTime', $result->getPosted());
    }

    /**
     * @covers FA\Dao\ImageDao::findAll
     */
    public function testFindAll()
    {
        $result = $this->dao->findAll();
        $this->assertInternalType('array', $result);
        $this->assertEquals(10, count($result));

        foreach ($result as $photo) {
            $this->assertInstanceOf('FA\Model\Photo\Photo', $photo);
        }
    }

    /**
     * @covers FA\Dao\ImageDao::findPage
     */
    public function testFindPage()
    {
        $page = 1;
        $perPage = 3;

        $result = $this->dao->findPage($page, $perPage);
        $this->assertInternalType('array', $result);
        $this->assertEquals(3, count($result));
    }

    /**
     * @covers FA\Dao\ImageDao::findNextImage
     * @dataProvider nextDayDataProvider
     */
    public function testFindNextImage($currentDay, $expected)
    {
        $actual = $this->dao->findNextImage($currentDay);
        $this->assertEquals($expected, $actual);
    }

    public function nextDayDataProvider()
    {
        return array(
            array(1, 2),
            array(7, 10),
            array(15, null),
        );
    }

    /**
     * @covers FA\Dao\ImageDao::findPreviousImage
     * @dataProvider previousDayDataProvider
     */
    public function testFindPreviousImage($currentDay, $expected)
    {
        $actual = $this->dao->findPreviousImage($currentDay);
        $this->assertEquals($expected, $actual);
    }

    public function previousDayDataProvider()
    {
        return array(
            array(1, null),
            array(7, 6),
            array(15, 11),
        );
    }

    /**
     * @covers FA\Dao\ImageDao::save
     */
    public function testSave()
    {
        $photo = new Photo();
        $photo->setDay(200);
        $photo->setPhotoId(7623527264);
        $result = $this->dao->save($photo);
        $this->assertEquals(1, $result);

        $image = $this->dao->find(200);
        $this->assertEquals(200, $image->getDay());
        $this->assertEquals(7623527264, $image->getPhotoId());
    }

    /**
     * @covers FA\Dao\ImageDao::save
     */
    public function testSaveDuplicateDayThrowsException()
    {
        $this->setExpectedException('PDOException', 'SQLSTATE[23000]: Integrity constraint violation: 19 column day is not unique');
        $photo = new Photo();
        $photo->setDay(7);
        $photo->setPhotoId(9627527264);
        $this->dao->save($photo);
    }

    /**
     * @covers FA\Dao\ImageDao::save
     */
    public function testSaveDuplicatePhotoIdThrowsException()
    {
        $this->setExpectedException('PDOException', 'SQLSTATE[23000]: Integrity constraint violation: 19 column photoId is not unique');
        $photo = new Photo();
        $photo->setDay(11);
        $photo->setPhotoId(7512338326);
        $this->dao->save($photo);
    }

    /**
     * @covers FA\Dao\ImageDao::delete
     */
    public function testDelete()
    {
        $photo = new Photo();
        $photo->setDay(1);

        $this->assertEquals(10, $this->dao->countImages());
        $this->assertEquals(1, $this->dao->delete($photo));
        $this->assertFalse($this->dao->find($photo->getDay()));
        $this->assertEquals(9, $this->dao->countImages());
    }

    /**
     * @covers FA\Dao\ImageDao::countImages
     */
    public function testCountImages()
    {
        $photo = new Photo();
        $photo->setDay(1);

        $count = $this->dao->countImages();
        $this->assertInternalType('int', $count);
        $this->assertEquals(10, $count);
        $this->dao->delete($photo);
        $this->assertEquals(9, $this->dao->countImages());
    }

    /**
     * @covers FA\Dao\ImageDao::findFirstImage
     */
    public function testFindFirstImage()
    {
        $posted = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-04-29 15:31:56');
        $expected = new Photo();
        $expected->setId(1);
        $expected->setDay(1);
        $expected->setPhotoId(7606616668);
        $expected->setPosted($posted);

        $actual = $this->dao->findFirstImage();

        $this->assertEquals($actual->getId(), $expected->getId());
        $this->assertEquals($actual->getDay(), $expected->getDay());
        $this->assertEquals($actual->getPhotoId(), $expected->getPhotoId());
        $this->assertEquals($actual->getPosted(), $expected->getPosted());
    }



    /**
     * @covers FA\Dao\ImageDao::findFirstImage
     */
    public function testFindFirstImageNoImagesInDatabase()
    {
        // Make sure the table is empty
        $this->db->exec('DELETE FROM images');
        $this->assertFalse($this->dao->findFirstImage());
    }
}
