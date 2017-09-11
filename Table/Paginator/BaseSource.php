<?php
/**
 * Abstract class to handle pagination in html tables.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Table\Paginator;

/**
 * Abstract class to handle pagination in html tables.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class BaseSource
{
    /**
     * The total number of rows to paginate.
     *
     * @var int
     */
    protected $_totalRowCount = 0;

    /**
     * The number of rows to display per page.
     *
     * @var int
     */
    protected $_rowsPerPage = 1;

    /**
     * The first page number.
     *
     * @var int
     */
    protected $_firstPage = 0;

    /**
     * The page to be displayed.
     *
     * @var int
     */
    protected $_currentPage = 0;



    /**
     * Constructor.
     *
     * @param array $options Optional array of config options.
     */
    public function __construct(array $options = array())
    {
        if (isset($options['rowsPerPage'])) {
            $this->setRowsPerPage($options['rowsPerPage']);
        }

        if (isset($options['firstPage'])) {
            $this->setFirstPage($options['firstPage']);
        }

        if (isset($options['currentPage'])) {
            $this->setCurrentPage($options['currentPage']);
        }
    }


    /**
     * Apply configuration parameters as passed from a rendered html table.
     *
     * @param array  $parameters The configuration parameters.
     * @param string $prefix     Optional prefix to use for parameter keys.
     *
     * @return void
     */
    public function initWithParameters(array $parameters, $prefix = '')
    {
        if (isset($parameters[$prefix . 'page'])) {
            $this->_currentPage = (int)$parameters[$prefix . 'page'];
        }
    }


    /**
     * Apply pagination to a data source.
     *
     * The source is count()'ed and the totalRowCount of the paginator
     * updated before the source is then sliced to represent a single page.
     *
     * Note: This WILL modify the supplied source.
     *
     * @param mixed &$source The source to paginate.
     *
     * @return void
     */
    public function paginate(&$source)
    {
        $this->_totalRowCount = count($source);
        $startingRow = $this->_rowsPerPage * ($this->_currentPage - $this->_firstPage);
        if ($startingRow < 0 || $startingRow > $this->_totalRowCount - $this->_firstPage) {
            $startingRow = 0;
            $this->_currentPage = (int)$this->_firstPage;
        }

        $this->_sliceDataSource($source, $startingRow, $this->_rowsPerPage);
    }


    /**
     * Return true if there are multiple pages.
     *
     * @return bool
     */
    public function isMultipage()
    {
        return ceil($this->_totalRowCount / $this->_rowsPerPage) > 1;
    }


    /**
     * Return details about all the pages.
     *
     * @return array
     */
    public function pages()
    {
        $pageCount = ceil($this->_totalRowCount / $this->_rowsPerPage);
        $results = array();
        for ($i = 0; $i < $pageCount; $i++) {
            $results[] = array(
             'index' => $i + $this->_firstPage,
             'label' => $i + 1,
             'currentPage' => ($i + $this->_firstPage == $this->_currentPage),
            );
        }
        return $results;
    }


    /**
     * Return the index of the page before the current page.
     *
     * @return int|null
     */
    public function previousPage()
    {
        if ($this->_currentPage == $this->_firstPage) {
            return -1;
        }
        return $this->_currentPage - 1;
    }


    /**
     * Return the index of the page after the current one, or null.
     *
     * @return int|null
     */
    public function nextPage()
    {
        if ($this->_currentPage == $this->getLastPage()) {
            return -1;
        }
        return $this->_currentPage + 1;
    }


    /**
     * Return the total number of rows in the data set.
     *
     * @return int
     */
    public function getTotalRowCount()
    {
        return $this->_totalRowCount;
    }


    /**
     * Set the total number of rows in the data set.
     *
     * @param int $count The total row count.
     *
     * @return void
     */
    public function setTotalRowCount($count)
    {
        if ($count < 0) {
            $count = 0;
        }
        $this->_totalRowCount = (int)$count;
    }


    /**
     * Return the first page number.
     *
     * @return int
     */
    public function getFirstPage()
    {
        return $this->_firstPage;
    }

    /**
     * Set the first page number.
     *
     * @param int $page The page number.
     *
     * @return void
     */
    public function setFirstPage($page)
    {
        $this->_firstPage = (int)$page;
        $this->setCurrentPage($this->_firstPage);
    }

    public function getLastPage()
    {
        return $this->_firstPage + ceil($this->_totalRowCount / $this->_rowsPerPage) - 1;
    }

    /**
     * Return the current page number.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Set the current page number.
     *
     * @param int $page The page number.
     *
     * @return void
     */
    public function setCurrentPage($page)
    {
        if ($page >= $this->_firstPage && $page <= $this->getLastPage()) {
            $this->_currentPage = (int)$page;
        }
    }


    /**
     * Return how many rows are shown on a page.
     *
     * @return int
     */
    public function getRowsPerPage()
    {
        return $this->_rowsPerPage;
    }


    /**
     * Set how many rows are shown on a page.
     *
     * @param int $rowsPerPage The number or rows to show per page.
     *
     * @return void
     */
    public function setRowsPerPage($rowsPerPage)
    {
        if ($rowsPerPage < 1) {
            $rowsPerPage = 1;
        }
        $this->_rowsPerPage = (int)$rowsPerPage;
    }


    /**
     * Perform a slice on the data source.
     *
     * @param mixed &$dataSource The data source.
     * @param int   $start       The first item's index in the slice.
     * @param int   $count       The number of items in the slice.
     *
     * @return void
     */
    protected function _sliceDataSource(&$dataSource, $start, $count)
    {
        throw new \Exception('Invalid call to method in Abstract class');
    }
}