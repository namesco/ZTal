<?php
/**
 * Table paginator unit tests with page numbers starting at zero.
 *
 * @category  Namesco
 * @package   UnitTesting
 * @author    Marcus Don <mdon@names.co.uk>
 * @copyright 2009-2017 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Tests\Table\Paginator;

// Shared environment configuration.
require_once __DIR__ . '/../../../sharedTestSetup.php';

/**
 * Table paginator unit tests with page numbers starting at zero.
 *
 * @category Namesco
 * @package  UnitTesting
 * @author   Marcus Don <mdon@names.co.uk>
 */
class PaginatorFirstPageZeroTests extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test pages method for with one page of data.
	 *
	 * @return void
	 */
	public function testPagesOne()
	{
		$numRows = 5;
		$rowsPerPage = 10;

		$expected = array(
			array('index' => 0, 'label' => 1, 'currentPage' => true),
		);

		$paginator = $this->_getPaginator($numRows, $rowsPerPage);

		$this->assertSame($expected, $paginator->pages());
	}

	/**
	 * Test pages method for with many pages of data.
	 *
	 * @return void
	 */
	public function testPagesMany()
	{
		$numRows = 53;
		$rowsPerPage = 20;
		$currentPage = 1;

		$expected = array(
			array('index' => 0, 'label' => 1, 'currentPage' => false),
			array('index' => 1, 'label' => 2, 'currentPage' => true),
			array('index' => 2, 'label' => 3, 'currentPage' => false),
		);

		$paginator = $this->_getPaginator($numRows, $rowsPerPage, $currentPage);

		$this->assertSame($expected, $paginator->pages());
	}

	/**
	 * Test pagination for page one of one.
	 *
	 * @return void
	 */
	public function testPaginatePageOneOfOne()
	{
		$numRows = 20;
		$rowsPerPage = 20;
		$currentPage = 0;

		$paginator = $this->_getPaginator($numRows, $rowsPerPage, $currentPage);

		$this->assertFalse($paginator->isMultipage());
		$this->assertSame(-1, $paginator->previousPage());
		$this->assertSame(0, $paginator->getCurrentPage());
		$this->assertSame(-1, $paginator->nextPage());
		$this->assertSame($numRows, $paginator->getTotalRowCount());
		$this->assertSame($rowsPerPage, $paginator->getRowsPerPage());
	}

	/**
	 * Test pagination for page one of many.
	 *
	 * @return void
	 */
	public function testPaginatePageOneOfMany()
	{
		$numRows = 100;
		$rowsPerPage = 20;
		$currentPage = 0;

		$paginator = $this->_getPaginator($numRows, $rowsPerPage, $currentPage);

		$this->assertTrue($paginator->isMultipage());
		$this->assertSame(-1, $paginator->previousPage());
		$this->assertSame(0, $paginator->getCurrentPage());
		$this->assertSame(1, $paginator->nextPage());
		$this->assertSame($numRows, $paginator->getTotalRowCount());
		$this->assertSame($rowsPerPage, $paginator->getRowsPerPage());
	}

	/**
	 * Test pagination for page two of many.
	 *
	 * @return void
	 */
	public function testPaginatePageTwoOfMany()
	{
		$numRows = 100;
		$rowsPerPage = 20;
		$currentPage = 1;

		$paginator = $this->_getPaginator($numRows, $rowsPerPage, $currentPage);

		$this->assertTrue($paginator->isMultipage());
		$this->assertSame(0, $paginator->previousPage());
		$this->assertSame(1, $paginator->getCurrentPage());
		$this->assertSame(2, $paginator->nextPage());
		$this->assertSame($numRows, $paginator->getTotalRowCount());
		$this->assertSame($rowsPerPage, $paginator->getRowsPerPage());
	}

	/**
	 * Test pagination for last page of many.
	 *
	 * @return void
	 */
	public function testPaginatePageLastOfMany()
	{
		$numRows = 100;
		$rowsPerPage = 20;
		$currentPage = 4;

		$paginator = $this->_getPaginator($numRows, $rowsPerPage, $currentPage);

		$this->assertTrue($paginator->isMultipage());
		$this->assertSame(3, $paginator->previousPage());
		$this->assertSame(4, $paginator->getCurrentPage());
		$this->assertSame(-1, $paginator->nextPage());
		$this->assertSame($numRows, $paginator->getTotalRowCount());
		$this->assertSame($rowsPerPage, $paginator->getRowsPerPage());
	}

	/**
	 * Get Paginator instance.
	 *
	 * @param integer $numRows     Number of rows.
	 * @param integer $rowsPerPage Number of rows per page.
	 * @param integer $currentPage Current page number (starting at 0).
	 *
	 * @return \Ztal\Table\Paginator\ArraySource
	 */
	private function _getPaginator($numRows, $rowsPerPage, $currentPage = 0)
	{
		$paginator = new \Ztal\Table\Paginator\ArraySource();

		$data = $this->_getData($numRows);
		$paginator->paginate($data);

		$paginator->setRowsPerPage($rowsPerPage);
		$paginator->setCurrentPage($currentPage);

		return $paginator;
	}

	/**
	 * Get data with which to populate paginator.
	 *
	 * @param integer $numRows Number of rows.
	 * @param integer $numCols Number of columns.
	 *
	 * @return array
	 */
	private function _getData($numRows, $numCols = 2)
	{
		$data = array();
		for ($rowNum = 1; $rowNum <= $numRows; $rowNum++) {
			$row = array();
			for ($colNum = 1; $colNum <= $numCols; $colNum++) {
				$row['col' . $colNum] = 'value' . (($rowNum - 1) * $numCols + $colNum);
			}
			$data['row' . $rowNum] = $row;
		}
		return $data;
	}
}
