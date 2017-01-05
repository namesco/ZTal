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

/**
 * Total cheat. Empty interface so we can load TALES. Must be in the global namespace.
 */
interface PHPTAL_Tales
{
}