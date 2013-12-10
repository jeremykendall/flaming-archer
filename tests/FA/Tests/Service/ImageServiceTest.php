<?php

namespace FA\Tests\Service;

use DateTime;
use FA\Model\Photo\Photo;
use FA\Model\Photo\Size;
use FA\Service\ImageService;

class ImageServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImageService
     */
    protected $service;

    /**
     * @var ImageDao
     */
    protected $dao;

    /**
     * @var FlickrService
     */
    protected $flickr;

    protected function setUp()
    {
        $this->dao = $this->getMockBuilder('FA\Dao\ImageDao')
            ->disableOriginalConstructor()
            ->getMock();
        $this->flickr = $this->getMockBuilder('FA\Service\FlickrService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new ImageService($this->dao, $this->flickr);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->service = null;
    }

    /**
     * @covers FA\Service\ImageService::find
     */
    public function testFind()
    {
        $data = array('day' => '1', 'photoId' => '7606616668');
        $photo = new Photo($data);

        $this->dao->expects($this->once())
            ->method('find')
            ->with($data['day'])
            ->will($this->returnValue($photo));

        $this->flickr->expects($this->once())
            ->method('find')
            ->with($photo)
            ->will($this->returnValue($photo));

        $this->service->find($data['day']);
    }

    public function testSearch()
    {
        $options = array(
            'option1' => 1,
            'option2' => 2,
        );

        $this->flickr->expects($this->once())
            ->method('search')
            ->with($options);

        $this->service->search($options);
    }

    public function testFindPage()
    {
        $photosDb = array(
            new Photo(array('day' => '3', 'photoId' => '33')),
            new Photo(array('day' => '2', 'photoId' => '22')),
            new Photo(array('day' => '1', 'photoId' => '11')),
        );

        $offset = 0;
        $itemCountPerPage = 3;

        $this->dao->expects($this->once())
            ->method('findPage')
            ->with($offset, $itemCountPerPage)
            ->will($this->returnValue($photosDb));

        $photosFlickr = array();

        foreach ($photosDb as $photo) {
            $photosFlickr[] = $photo->setSize('large', new Size());
        }

        $this->flickr->expects($this->once())
            ->method('findPhotos')
            ->with($photosDb)
            ->will($this->returnValue($photosFlickr));

        $result = $this->service->findPage($offset, $itemCountPerPage);

        $this->assertEquals($photosFlickr, $result);
    }

    public function testFindNextImage()
    {
        $this->dao->expects($this->once())
            ->method('findNextImage')
            ->with(7)
            ->will($this->returnValue(10));

        $this->service->findNextImage(7);
    }

    public function testFindPreviousImage()
    {
        $this->dao->expects($this->once())
            ->method('findPreviousImage')
            ->with(10)
            ->will($this->returnValue(7));

        $this->service->findPreviousImage(10);
    }

    public function testFindImageDoesNotExist()
    {
        $this->dao->expects($this->once())
            ->method('find')
            ->with('222')
            ->will($this->returnValue(false));

        $this->flickr->expects($this->never())->method('find');

        $this->assertNull($this->service->find('222'));
    }

    /**
     * @covers FA\Service\ImageService::findAll
     */
    public function testFindAll()
    {
        $photosDb = array(
            new Photo(array('day' => '3', 'photoId' => '33')),
            new Photo(array('day' => '2', 'photoId' => '22')),
            new Photo(array('day' => '1', 'photoId' => '11')),
        );

        $this->dao->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($photosDb));

        $photosFlickr = array();

        foreach ($photosDb as $photo) {
            $photosFlickr[] = $photo->setSize('large', new Size());
        }

        $this->flickr->expects($this->once())
            ->method('findPhotos')
            ->with($photosDb)
            ->will($this->returnValue($photosFlickr));

        $result = $this->service->findAll();

        $this->assertEquals($photosFlickr, $result);
    }

    /**
     * @covers FA\Service\ImageService::save
     */
    public function testSave()
    {
        $photo = new Photo(array('day' => 200, 'photoId' => 999999));
        $this->dao->expects($this->once())
            ->method('save')
            ->with($photo)
            ->will($this->returnValue(true));

        $this->assertTrue($this->service->save($photo));
    }

    /**
     * @covers FA\Service\ImageService::delete
     */
    public function testDelete()
    {
        $photo = new Photo(array('day' => 200));
        $this->dao->expects($this->once())
            ->method('delete')
            ->with($photo)
            ->will($this->returnValue(true));

        $this->assertTrue($this->service->delete($photo));
    }

    /**
     * @covers FA\Service\ImageService::getProjectDay
     * @covers FA\Model\Photo\Photo::getPosted
     * @dataProvider getProjectDayDataProvider
     */
    public function testGetProjectDay($testDate, $projectDay)
    {
        $firstImage = array(
            'id' => 1,
            'day' => 1,
            'photoId' => 7606616668,
            'posted' => '2012-07-29 15:31:56',
        );

        $photo = new Photo($firstImage);

        $date = new DateTime($testDate);

        $this->dao->expects($this->once())
            ->method('findFirstImage')
            ->will($this->returnValue($photo));

        $this->assertEquals($projectDay, $this->service->getProjectDay($date));
    }

    /**
     * @covers FA\Service\ImageService::getProjectDay
     * @dataProvider getProjectDayDataProvider
     */
    public function testGetProjectDayWhenNoImagesExist()
    {
        $this->dao->expects($this->once())
            ->method('findFirstImage')
            ->will($this->returnValue(false));

        $this->assertEquals(1, $this->service->getProjectDay());
    }

    public function testCountImages()
    {
        $this->dao->expects($this->once())
            ->method('countImages');

        $this->service->countImages();
    }

    public function getProjectDayDataProvider()
    {
        return array(
            array('2012-07-30', 2),
            array('2012-07-31', 3),
            array('2013-07-28', 365),
        );
    }
}
