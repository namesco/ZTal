<?php
/**
 * PHPUnit test bootstrap.
 *
 * @category  Namesco
 * @package   UnitTesting
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

//shared environment configuration
require_once dirname(__FILE__) . '/sharedTestSetup.php';

//Test classes
require_once 'PlaceholderTest.php';

/**
 * Top level config for all Ztal Unit Tests.
 *
 * @category Namesco
 * @package  PHPUnit
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

class AllTests
{
	/**
	 * Sets up the contents of this suite of tests.
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	static public function suite()
	{
		// Create an instance of a test suite
		$suite = new PHPUnit_Framework_TestSuite('Ztal');

		$suite->addTestSuite('PlaceholderTest');

		return $suite;
	}
}

