<?php
/**
 * Basic model for use in the table examples.
 *
 * @category  Namesco
 * @package   ZtalExample
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Basic model for use in the table examples.
 *
 * @category Namesco
 * @package  ZtalExample
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

class Application_Model_Basic
{
	/**
	 * Age.
	 *
	 * @var int
	 */
	protected $_age;

	/**
	 * Height.
	 *
	 * @var float
	 */
	protected $_height;

	/**
	 * Constructor.
	 *
	 * @param int   $age    The age.
	 * @param float $height The height.
	 */
	public function __construct($age, $height)
	{
		$this->_age = $age;
		$this->_height = $height;
	}
	
	/**
	 * Return the age.
	 *
	 * @return int
	 */
	public function getAge()
	{
		return $this->_age;
	}
	
	/**
	 * Return the height.
	 *
	 * @return float
	 */
	public function getHeight()
	{
		return $this->_height;
	}
	
	
	/**
	 * Sort method compatible with the example table sort call.
	 *
	 * @param Application_Model_Basic $otherItem The item to compare $this with.
	 * 
	 * @return int
	 */
	public function sortByAge(Application_Model_Basic $otherItem)
	{
		$thisValue = $this->getAge();
		$otherValue = $otherItem->getAge();
		
		if ($thisValue == $otherValue) {
			return 0;
		}
		return ($thisValue > $otherValue) ? 1 : -1;
	}


	/**
	 * Sort method compatible with the example table sort call.
	 *
	 * @param Application_Model_Basic $otherItem The item to compare $this with.
	 * 
	 * @return int
	 */
	public function sortByHeight(Application_Model_Basic $otherItem)
	{
		$thisValue = $this->getHeight();
		$otherValue = $otherItem->getHeight();
		
		if ($thisValue == $otherValue) {
			return 0;
		}
		return ($thisValue > $otherValue) ? 1 : -1;
	}
}
