<?php
/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace FA;

use FA\Paginator\Adapter\DbAdapter;
use Zend\Paginator\Paginator;

/**
 * Returns a Paginator
 */
class Pagination
{
    /**
     * @var DbAdapter
     */
    private $dbAdapter;

    /**
     * Public constructor
     *
     * @param DbAdapter $dbAdapter DbAdapter
     */
    public function __construct(DbAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Creates a paginator
     *
     * @return Paginator Zend Paginator
     */
    public function newPaginator($currentPage = 1, $perPage = 25)
    {
        $paginator = new Paginator($this->dbAdapter);
        $paginator->setItemCountPerPage($perPage);
        $paginator->setCurrentPageNumber($currentPage);

        return $paginator;
    }
}
