<?php

namespace FA\Tests;

use FA\Pagination;
use Zend\Paginator\Paginator;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    private $dbAdapter;
    private $pagination;

    protected function setUp()
    {
        $this->dbAdapter = $this->getMockBuilder('FA\Paginator\Adapter\DbAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pagination = new Pagination($this->dbAdapter);
    }

    public function testNewPaginator()
    {
        $paginator = $this->pagination->newPaginator(3, 1);
        $this->assertInstanceOf('Zend\Paginator\Paginator', $paginator);
        $this->assertEquals(3, $paginator->getCurrentPageNumber());
        $this->assertEquals(1, $paginator->getItemCountPerPage());
    }

    public function testNewPaginatorDefaultArguments()
    {
        $paginator = $this->pagination->newPaginator();
        $this->assertInstanceOf('Zend\Paginator\Paginator', $paginator);
        $this->assertEquals(1, $paginator->getCurrentPageNumber());
        $this->assertEquals(25, $paginator->getItemCountPerPage());
    }
}
