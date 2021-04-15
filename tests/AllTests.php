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

// Shared environment configuration.
require_once __DIR__ . '/sharedTestSetup.php';

// Test classes.
require_once __DIR__ . '/Tests/AutoloaderTests.php';
require_once __DIR__ . '/Tests/Table/Paginator/PaginatorFirstPageOneTests.php';
require_once __DIR__ . '/Tests/Table/Paginator/PaginatorFirstPageZeroTests.php';

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
        $suite = new \PHPUnit\Framework\TestSuite('Ztal');

        $suite->addTestSuite('Ztal\Tests\AutoloaderTests');
        $suite->addTestSuite('Ztal\Tests\Table\Paginator\PaginatorFirstPageOneTests');
        $suite->addTestSuite('Ztal\Tests\Table\Paginator\PaginatorFirstPageZeroTests');

        return $suite;
    }
}