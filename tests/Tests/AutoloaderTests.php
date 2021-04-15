<?php
/**
 * Autoloader unit tests.
 *
 * @category  Namesco
 * @package   UnitTesting
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2013 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Tests;

// Shared environment configuration.
require_once __DIR__ . '/../sharedTestSetup.php';

// Class under test.
require_once __DIR__ . '/../../Autoloader.php';

/**
 * Autoloader unit tests.
 *
 * @category Namesco
 * @package  UnitTesting
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
class AutoloaderTests extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the compatibility layer (class aliasing) works for non-renamed classes.
     *
     * @param string $legacyClassName Name of the compatibility-mode class.
     * @param string $className       Actual class name in the codebase.
     *
     * @return void
     *
     * @dataProvider compatibilityClassesDataProvider
     */
    public function testAutoloadingCompatibilityClasses($legacyClassName, $className)
    {
        $this->assertTrue(\Ztal\Autoloader::autoloadCompatibility($legacyClassName), 'Failed to autoload.');
        $this->assertTrue(class_exists($className), 'Class doesn\'t exist.');
    }

    /**
     * Data provider for testAutoloadingNonRenamedClasses.
     *
     * @return array
     */
    public function compatibilityClassesDataProvider()
    {
        // Add some more classes here if you like. They can't have any external requirements, though.
        return array(
         // Non-renamed classes first.
         array('Ztal_Table_Row', 'Ztal\\Table\\Row'),

         // Renamed (and mapped) classes.
         array('Ztal_Tales_Array', 'Ztal\\Tales\\ArrayUtils'),
         array('Ztal_Table_Abstract', 'Ztal\\Table\\Base'),
         array('Ztal_Table_Column_Abstract', 'Ztal\\Table\\Column\\BaseSource'),
         array('Ztal_Table_Paginator_Abstract', 'Ztal\\Table\\Paginator\\BaseSource'),
        );
    }
}