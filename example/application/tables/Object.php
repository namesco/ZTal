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

class Application_Table_Object extends Ztal_Table_Abstract
{
	/**
	 * Setup the columns for the table.
	 *
	 * @return void
	 */
	protected function _init()
	{
		// Setup the columns
		$this->appendColumn(new Ztal_Table_Column_Object('age',
			'getAge', array('sortField' => 'sortByAge')));

		$this->appendColumn(new Ztal_Table_Column_Object('height',
			'getHeight', array('sortField' => 'sortByHeight')));



		// Set defaults for the table as a whole
		$this->setId('objectTable');
		$this->setBaseUri('/default/index/object-table');
		$this->setSortColumn('age');

		// Configure the paginator
		$paginator = new Ztal_Table_Paginator_Array();
		$paginator->setRowsPerPage(4);

		$this->setPaginator($paginator);
	}
}
