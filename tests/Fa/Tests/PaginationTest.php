<?php

namespace Fa\Tests;

use Fa\Pagination;
use Zend\Paginator\Paginator;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    public function testNewPaginator()
    {
        $images = array(
            'image1',
            'image2',
            'image3',
            'image4',
            'image5',
        );

        $pagination = new Pagination();
        $paginator = $pagination->newPaginator($images, 3, 1);
        $this->assertInstanceOf('Zend\Paginator\Paginator', $paginator);
        $this->assertEquals(3, $paginator->getCurrentPageNumber());
        $this->assertEquals(1, $paginator->getItemCountPerPage());
    }

    public function testNewPaginatorDefaultArguments()
    {
        $images = array(
            'image1',
            'image2',
            'image3',
            'image4',
            'image5',
        );

        $pagination = new Pagination();
        $paginator = $pagination->newPaginator();
        $this->assertInstanceOf('Zend\Paginator\Paginator', $paginator);
        $this->assertEquals(1, $paginator->getCurrentPageNumber());
        $this->assertEquals(25, $paginator->getItemCountPerPage());
    }
}
