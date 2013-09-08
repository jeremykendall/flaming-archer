<?php

namespace FA\Tests\Service;

use DateTime;
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
        $imageData = array('day' => '1', 'photo_id' => '7606616668');
        $imageSizes = array('sizes' => array('size' => array()));

        $this->dao->expects($this->once())
                ->method('find')
                ->with($imageData['day'])
                ->will($this->returnValue($imageData));

        $this->flickr->expects($this->once())
                ->method('find')
                ->with($imageData['photo_id'])
                ->will($this->returnValue($imageSizes));

        $result = $this->service->find($imageData['day']);

        $this->assertEquals(array_merge($imageData, $imageSizes), $result);
    }

    public function testFindPage()
    {
        $imageData = array(
            array('day' => '3', 'photo_id' => '33'),
            array('day' => '2', 'photo_id' => '22'),
            array('day' => '1', 'photo_id' => '11'),
        );

        $imageSizes = array(
            array('sizes' => array(33)),
            array('sizes' => array(22)),
            array('sizes' => array(11)),
        );

        $offset = 0;
        $itemCountPerPage = 3;

        $page = $imageData;

        $this->dao->expects($this->once())
            ->method('findPage')
            ->with($offset, $itemCountPerPage)
            ->will($this->returnValue($page));

        // The Flickr service should be called as many times as there are
        // data elements returned from the dao
        foreach ($imageData as $index => $image) {

            $expected[] = array_merge($image, $imageSizes[$index]);

            $this->flickr->expects($this->at($index))
                ->method('find')
                ->with($image['photo_id'])
                ->will($this->returnValue($imageSizes[$index]));
        }

        $result = $this->service->findPage($offset, $itemCountPerPage);

        $this->assertEquals($expected, $result);
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
        $imageData = array(
            array('day' => '1', 'photo_id' => '11'),
            array('day' => '2', 'photo_id' => '22'),
            array('day' => '3', 'photo_id' => '33')
        );

        $imageSizes = array(
            array('sizes' => array(11)),
            array('sizes' => array(22)),
            array('sizes' => array(33))
        );

        $this->dao->expects($this->once())
                ->method('findAll')
                ->will($this->returnValue($imageData));

        $expected = array();

        // The Flickr service should be called as many times as there are
        // data elements returned from the dao
        foreach ($imageData as $index => $image) {

            $expected[] = array_merge($image, $imageSizes[$index]);

            $this->flickr->expects($this->at($index))
                    ->method('find')
                    ->with($image['photo_id'])
                    ->will($this->returnValue($imageSizes[$index]));
        }

        $result = $this->service->findAll();

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers FA\Service\ImageService::save
     */
    public function testSave()
    {
        $this->dao->expects($this->once())
                ->method('save')
                ->with(array('day' => 200, 'photo_id' => 999999))
                ->will($this->returnValue(1));

        $this->assertEquals(1, $this->service->save(array('day' => 200, 'photo_id' => 999999)));
    }

    /**
     * @covers FA\Service\ImageService::delete
     */
    public function testDelete()
    {
        $this->dao->expects($this->once())
                ->method('delete')
                ->with(200)
                ->will($this->returnValue(1));

        $this->assertEquals(1, $this->service->delete(200));
    }

    /**
     * @covers FA\Service\ImageService::getProjectDay
     * @dataProvider getProjectDayDataProvider
     */
    public function testGetProjectDay($testDate, $projectDay)
    {
        $firstImage = array(
            'id' => 1,
            'day' => 1,
            'photo_id' => 7606616668,
            'posted' => '2012-07-29 15:31:56',
        );

        $date = new DateTime($testDate);

        $this->dao->expects($this->once())
                ->method('findFirstImage')
                ->will($this->returnValue($firstImage));

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
