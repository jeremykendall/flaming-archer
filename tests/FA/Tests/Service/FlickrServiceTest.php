<?php

namespace FA\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;
use FA\Model\Photo\Size;
use FA\Service\FlickrService;

/**
 * @group internet
 */
class FlickrServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlickrService
     */
    protected $service;

    /**
     * @var array
     */
    protected static $config;

    /**
     * @var ArrayCollection
     */
    protected $sizes;

    /**
     * @var Photo
     */
    protected $photo;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$config = require_once APPLICATION_PATH . '/config.php';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->service = new FlickrService(self::$config['flickr.api.key']);

        $info = $this->getInfoResult();
        $this->photo = new Photo();
        $this->photo->setPhotoId($info['photo']['id']);
        $this->photo->setTitle($info['photo']['title']['_content']);
        $this->photo->setDescription($info['photo']['description']['_content']);
        $this->photo->setTags($info['photo']['tags']['tag']);

        $sizesResult = $this->getSizesResult();
        $this->sizes = new ArrayCollection();

        foreach ($sizesResult['sizes']['size'] as $result) {
            $size = new Size();
            $size->setLabel($result['label']);
            $size->setWidth($result['width']);
            $size->setHeight($result['height']);
            $size->setSource($result['source']);
            $size->setUrl($result['url']);
            $this->sizes->set($size->getLabel(), $size);
        }

        $this->photo->setSizes($this->sizes);
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
     * @covers FA\Service\FlickrService::__construct
     */
    public function testCreation()
    {
        $this->assertInstanceOf('FA\Service\FlickrService', $this->service);
        $this->assertInstanceOf('FA\Service\FlickrInterface', $this->service);
    }

    /**
     * @covers FA\Service\FlickrService::find
     * @covers FA\Service\FlickrService::getSizes
     * @covers FA\Service\FlickrService::getInfo
     * @covers FA\Service\FlickrService::makeRequest
     * @covers FA\Model\Photo\Size::getLabel
     * @covers FA\Model\Photo\Size::setLabel
     * @covers FA\Model\Photo\Size::setWidth
     * @covers FA\Model\Photo\Size::setHeight
     * @covers FA\Model\Photo\Size::setSource
     * @covers FA\Model\Photo\Size::setUrl
     */
    public function testFind()
    {
        $photoId = 5977249629;

        $photo = new Photo();
        $photo->setPhotoId($photoId);

        $actual = $this->service->find($photo);

        $this->assertEquals($this->photo, $actual);
        $this->assertEquals('Jonathan at the Young Avenue Deli', $photo->getTitle());
    }

    protected function getSizesResult()
    {
        $result = <<<JSON
{ "sizes": { "canblog": 0, "canprint": 0, "candownload": 0,
    "size": [
      { "label": "Square", "width": 75, "height": 75, "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_s.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/sq\/", "media": "photo" },
      { "label": "Large Square", "width": "150", "height": "150", "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_q.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/q\/", "media": "photo" },
      { "label": "Thumbnail", "width": 100, "height": 67, "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_t.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/t\/", "media": "photo" },
      { "label": "Small", "width": "240", "height": "160", "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_m.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/s\/", "media": "photo" },
      { "label": "Small 320", "width": "320", "height": "213", "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_n.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/n\/", "media": "photo" },
      { "label": "Medium", "width": "500", "height": "333", "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/m\/", "media": "photo" },
      { "label": "Medium 640", "width": "640", "height": "426", "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_z.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/z\/", "media": "photo" },
      { "label": "Large", "width": "1024", "height": "681", "source": "http:\/\/farm7.staticflickr.com\/6132\/5977249629_c204d31e3d_b.jpg", "url": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/sizes\/l\/", "media": "photo" }
    ] }, "stat": "ok" }
JSON;
    
        return json_decode($result, true);
    }

    protected function getInfoResult()
    {
        $result = <<<JSON
{ "photo": { "id": "5977249629", "secret": "c204d31e3d", "server": "6132", "farm": 7, "dateuploaded": "1311682846", "isfavorite": 0, "license": 0, "safety_level": 0, "rotation": 0,
    "owner": { "nsid": "27552439@N00", "username": "Jeremy Kendall", "realname": "Jeremy Kendall", "location": "Nashville, TN, USA", "iconserver": "3719", "iconfarm": 4, "path_alias": "jeremykendall" },
    "title": { "_content": "Jonathan at the Young Avenue Deli" },
    "description": { "_content": "" },
    "visibility": { "ispublic": 1, "isfriend": 0, "isfamily": 0 },
    "dates": { "posted": "1311682846", "taken": "2011-05-12 18:34:09", "takengranularity": 0, "lastupdate": "1356335573" }, "views": "116",
    "editability": { "cancomment": 0, "canaddmeta": 0 },
    "publiceditability": { "cancomment": 1, "canaddmeta": 0 },
    "usage": { "candownload": 0, "canblog": 0, "canprint": 0, "canshare": 1 },
    "comments": { "_content": 0 },
    "notes": {
      "note": [

      ] },
    "people": { "haspeople": 0 },
    "tags": {
      "tag": [
        { "id": "688757-5977249629-65", "author": "27552439@N00", "raw": "Birthday", "_content": "birthday", "machine_tag": 0 },
        { "id": "688757-5977249629-4905", "author": "27552439@N00", "raw": "Jonathan", "_content": "jonathan", "machine_tag": 0 },
        { "id": "688757-5977249629-106978", "author": "27552439@N00", "raw": "Young Avenue Deli", "_content": "youngavenuedeli", "machine_tag": 0 },
        { "id": "688757-5977249629-5373", "author": "27552439@N00", "raw": "Memphis", "_content": "memphis", "machine_tag": 0 },
        { "id": "688757-5977249629-4075", "author": "27552439@N00", "raw": "TN", "_content": "tn", "machine_tag": 0 },
        { "id": "688757-5977249629-4074", "author": "27552439@N00", "raw": "United States", "_content": "unitedstates", "machine_tag": 0 },
        { "id": "688757-5977249629-2296", "author": "27552439@N00", "raw": "US", "_content": "us", "machine_tag": 0 }
      ] },
    "location": { "latitude": 35.119697, "longitude": -89.991674, "accuracy": 14, "context": 0,
      "neighbourhood": { "_content": "Cooper-Young", "place_id": "QXO9e0NTWrhbNBPzeQ", "woeid": "28288874" },
      "locality": { "_content": "Memphis", "place_id": "SnL4qv1TVr4RmNxb", "woeid": "2449323" },
      "county": { "_content": "Shelby", "place_id": "Svc7lW1QUL94_ZPIhQ", "woeid": "12589990" },
      "region": { "_content": "Tennessee", "place_id": "PgNbvuhTUb5yTgXh", "woeid": "2347601" },
      "country": { "_content": "United States", "place_id": "nz.gsghTUb4c2WAecA", "woeid": "23424977" }, "place_id": "QXO9e0NTWrhbNBPzeQ", "woeid": "28288874" },
    "geoperms": { "ispublic": 1, "iscontact": 0, "isfriend": 0, "isfamily": 0 },
    "urls": {
      "url": [
        { "type": "photopage", "_content": "http:\/\/www.flickr.com\/photos\/jeremykendall\/5977249629\/" }
      ] }, "media": "photo" }, "stat": "ok" }
JSON;

        return json_decode($result, true);
    }
}
