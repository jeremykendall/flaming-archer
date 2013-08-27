<?php

namespace FA\Tests\Dao;

use FA\Dao\ImageDao;

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
     */
    public function testFind()
    {
        $result = $this->dao->find(1);
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, $result['day']);
        $this->assertEquals(7606616668, $result['photo_id']);
    }

    /**
     * @covers FA\Dao\ImageDao::findAll
     */
    public function testFindAll()
    {
        $result = $this->dao->findAll();
        $this->assertInternalType('array', $result);
        $this->assertEquals(10, count($result));
    }

    /**
     * @covers FA\Dao\ImageDao::save
     */
    public function testSave()
    {
        $result = $this->dao->save(array('day' => 200, 'photo_id' => 7623527264));
        $this->assertEquals(1, $result);

        $image = $this->dao->find(200);
        $this->assertEquals(200, $image['day']);
        $this->assertEquals(7623527264, $image['photo_id']);
    }

    /**
     * @covers FA\Dao\ImageDao::save
     */
    public function testSaveDuplicateDayThrowsException()
    {
        $this->setExpectedException('PDOException', 'SQLSTATE[23000]: Integrity constraint violation: 19 column day is not unique');
        $this->dao->save(array('day' => 7, 'photo_id' => 9627527264));
    }

    /**
     * @covers FA\Dao\ImageDao::save
     */
    public function testSaveDuplicatePhotoIdThrowsException()
    {
        $this->setExpectedException('PDOException', 'SQLSTATE[23000]: Integrity constraint violation: 19 column photo_id is not unique');
        $this->dao->save(array('day' => 11, 'photo_id' => 7512338326));
    }

    /**
     * @covers FA\Dao\ImageDao::delete
     */
    public function testDelete()
    {
        $this->assertEquals(10, $this->dao->countImages());
        $this->assertEquals(1, $this->dao->delete(1));
        $this->assertFAlse($this->dao->find(1));
        $this->assertEquals(9, $this->dao->countImages());
    }

    /**
     * @covers FA\Dao\ImageDao::countImages
     */
    public function testCountImages()
    {
        $count = $this->dao->countImages();
        $this->assertInternalType('int', $count);
        $this->assertEquals(10, $count);
        $this->dao->delete(1);
        $this->dao->delete(2);
        $this->dao->delete(3);
        $this->dao->delete(4);
        $this->assertEquals(6, $this->dao->countImages());
    }

    /**
     * @covers FA\Dao\ImageDao::findFirstImage
     */
    public function testFindFirstImage()
    {
        $expected = array(
            'id' => 1,
            'day' => 1,
            'photo_id' => 7606616668,
            'posted' => '2013-04-29 15:31:56',
        );

        $actual = $this->dao->findFirstImage();

       $this->assertEquals($actual, $expected); 
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
