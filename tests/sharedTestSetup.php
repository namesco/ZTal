<?php
/**
 * PHPUnit test shared environment setup.
 *
 * @category  Namesco
 * @package   UnitTesting
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

if (function_exists('xdebug_disable')) {
	xdebug_disable();
}

require_once 'PHPUnit/Autoload.php';

// Configure autoloaders for Zend and Dada
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');