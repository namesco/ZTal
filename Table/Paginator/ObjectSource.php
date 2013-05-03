<?php
/**
 * Class to handle object pagination in html tables.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Table\Paginator;

/**
 * Class to handle object pagination in html tables.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class ObjectSource extends BaseSource
{
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
		if (method_exists($dataSource, 'slice')) {
			$dataSource = $dataSource->slice($start, $count);
		} else {
			throw new \Exception('Unable to paginate object: no slice method');
		}
	}
}