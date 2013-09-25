<?php

namespace FA\Tests\Social;

use Doctrine\Common\Collections\ArrayCollection;
use FA\Model\Photo\Photo;
use FA\Model\Photo\Size;
use FA\Social\MetaTags;

class MetaTagsTest extends \PHPUnit_Framework_TestCase
{
    protected $metaTags;

    protected $request;

    protected $photo;

    protected $profile;

    protected $openGraphTags;

    protected $twitterCard;

    protected function setUp()
    {
        $this->photo = $this->getPhoto();

        $this->request = $this->getMockBuilder('Slim\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->profile = array(
            'site_name' => '365 Days of Photography',
            'tagline' => "I've combined my love of photography and open source software to create this photo-a-day tool for hackers.  Here are my 365.",
            'twitter_username' => '@JeremyKendall',
        );

        $this->metaTags = new MetaTags(
            $this->request, 
            $this->photo, 
            $this->profile
        );

        $this->openGraphTags = array(
            'og:url' => 'http://flaming-archer.dev/day/10',
            'og:title' => 'Untitled | Day 10',
            'og:description' => "I've combined my love of photography and open source software to create this photo-a-day tool for hackers.  Here are my 365.",
            'og:image' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_b.jpg',
        );

        $this->twitterCard = array(
            'twitter:card' => 'photo',
            'twitter:title' => 'Untitled | Day 10',
            'twitter:image:src' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_b.jpg',
            'twitter:image:width' => 665,
            'twitter:image:height' => 1000,
            'twitter:creator' => '@JeremyKendall',
            'twitter:site' => '@JeremyKendall',
        );
    }

    public function testGetOpenGraphTags()
    {
        $this->request->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://flaming-archer.dev'));

        $this->request->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/day/10'));

        $this->assertEquals($this->openGraphTags, $this->metaTags->getOpenGraphTags());
    }

    public function testGetOpenGraphTagsWithPhotoDescription()
    {
        $this->photo->setDescription('Photo description');

        $metaTags = new MetaTags($this->request, $this->photo, $this->profile);

        $this->request->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://flaming-archer.dev'));

        $this->request->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/day/10'));

        $this->openGraphTags['og:description'] = 'Photo description';

        $this->assertEquals($this->openGraphTags, $metaTags->getOpenGraphTags());
    }

    public function testGetTwiterPhotoCard()
    {
        $this->assertEquals($this->twitterCard, $this->metaTags->getTwitterPhotoCard());
    }

    public function testGetTags()
    {
        $this->request->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://flaming-archer.dev'));

        $this->request->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/day/10'));

        $tags = array_merge(
            $this->openGraphTags,
            $this->twitterCard
        );

        $this->assertEquals($tags, $this->metaTags->getTags());
    }

    protected function getPhoto()
    {
        $photoData = array (
            'id' => '10',
            'day' => '10',
            'photoId' => '7623533156',
            'posted' => '2013-08-10 12:19:54',
        );

        $sizeData = array (
            'Square' => array (
                'label' => 'Square',
                'width' => 75,
                'height' => 75,
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_s.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/sq/',
                'media' => 'photo',
            ),
            'Large Square' => array (
                'label' => 'Large Square',
                'width' => '150',
                'height' => '150',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_q.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/q/',
                'media' => 'photo',
            ),
            'Thumbnail' => array (
                'label' => 'Thumbnail',
                'width' => '67',
                'height' => '100',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_t.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/t/',
                'media' => 'photo',
            ),
            'Small' => array (
                'label' => 'Small',
                'width' => '160',
                'height' => '240',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_m.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/s/',
                'media' => 'photo',
            ),
            'Small 320' => array (
                'label' => 'Small 320',
                'width' => 213,
                'height' => '320',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_n.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/n/',
                'media' => 'photo',
            ),
            'Medium' => array (
                'label' => 'Medium',
                'width' => '333',
                'height' => '500',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/m/',
                'media' => 'photo',
            ),
            'Medium 640' => array (
                'label' => 'Medium 640',
                'width' => '426',
                'height' => '640',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_z.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/z/',
                'media' => 'photo',
            ),
            'Medium 800' => array (
                'label' => 'Medium 800',
                'width' => 532,
                'height' => '800',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_c.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/c/',
                'media' => 'photo',
            ),
            'Large' => array (
                'label' => 'Large',
                'width' => '665',
                'height' => '1000',
                'source' => 'http://farm8.staticflickr.com/7115/7623533156_a557f0ecc6_b.jpg',
                'url' => 'http://www.flickr.com/photos/jeremykendall/7623533156/sizes/l/',
                'media' => 'photo',
            ),
        );

        $sizes = new ArrayCollection();

        foreach ($sizeData as $size => $data) {
            $sizes->set($size, new Size($data));
        }

        $photo = new Photo($photoData);
        $photo->setSizes($sizes);

        return $photo;
    }
}
