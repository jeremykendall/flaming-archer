<?php

namespace Fa;

use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;

class Pagination
{
    public function newPaginator(array $images = array(), $currentPage = 1, $perPage = 25)
    {
        $paginator = new Paginator(new ArrayAdapter($images));
        $paginator->setItemCountPerPage($perPage);
        $paginator->setCurrentPageNumber($currentPage);

        return $paginator;
    }
}
