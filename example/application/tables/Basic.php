<?php
/**
 * Example Table.
 *
 * @category  Namesco
 * @package   ZtalExample
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Example Table.
 *
 * @category Namesco
 * @package  ZtalExample
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

class Application_Table_Basic extends Ztal_Table_Abstract
{
	/**
	 * Setup the columns for the table.
	 *
	 * @return void
	 */
	protected function _init()
	{
		// Setup the columns
		$this->appendColumn(new Ztal_Table_Column_Array('firstColumn',
			'col1', array('sortField' => 'col1')));

		$this->appendColumn(new Ztal_Table_Column_Array('secondColumn',
			'col2', array('sortField' => 'col2'))); 



		// Set defaults for the table as a whole
		$this->setId('basicTable');
		$this->setBaseUri('/default/index/table');
		$this->setSortColumn('firstColumn');
		
		// Configure the paginator
		$paginator = new Ztal_Table_Paginator_Array();
		$paginator->setRowsPerPage(2);
		
		$this->setPaginator($paginator);
	}
}
