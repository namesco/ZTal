<?php
/**
 * Compatibility loader for non-namespaced code.
 *
 * This file provides and autoloader and class aliasing for projects that have
 * not yet updated to use the namespaced version of Ztal.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2013 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal;

/**
 * Compatibility loader for non-namespaced code.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
class Autoloader
{
    /**
     * Some classes were renamed, so we maintain a map of them here for the compatibility autoloader.
     *
     * @var array
     */
    static protected $_compatibilityClassMap = array(
    'Ztal_Table_Abstract' => 'Ztal\\Table\\Base',

    'Ztal_Table_Column_Abstract' => 'Ztal\\Table\\Column\\BaseSource',
    'Ztal_Table_Column_Array' => 'Ztal\\Table\\Column\\ArraySource',
    'Ztal_Table_Column_Object' => 'Ztal\\Table\\Column\\ObjectSource',

    'Ztal_Table_Paginator_Abstract' => 'Ztal\\Table\\Paginator\\BaseSource',
    'Ztal_Table_Paginator_Array' => 'Ztal\\Table\\Paginator\\ArraySource',
    'Ztal_Table_Paginator_Object' => 'Ztal\\Table\\Paginator\\ObjectSource',

    'Ztal_Tales_Array' => 'Ztal\\Tales\\ArrayUtils',
    );

    /**
     * Register this autoloader's standard loader with the SPL autoloader stack.
     *
     * @return void
     */
    static public function register()
    {
        spl_autoload_register('Ztal\\Autoloader::autoload');
    }

    /**
     * Register this autoloader's compatibility loader with the SPL autoloader stack.
     *
     * @return void
     */
    static public function registerCompatibility()
    {
        spl_autoload_register('Ztal\\Autoloader::autoloadCompatibility');
    }

    /**
     * Ztal compatibility autoloading.
     *
     * @param string $legacyClassName Class name to load.
     *
     * @return bool
     */
    static public function autoloadCompatibility($legacyClassName)
    {
        // Simply pass through requests to load the namespaced classes.
        if (substr($legacyClassName, 0, 5) == 'Ztal\\') {
            return static::autoload($legacyClassName);
        }

        // Don't attempt to load any classes but ours.
        if (substr($legacyClassName, 0, 5) != 'Ztal_') {
            return false;
        }

        if (array_key_exists($legacyClassName, static::$_compatibilityClassMap)) {
            // We have an entry in the class map for this class, so use it.
            $className = static::$_compatibilityClassMap[$legacyClassName];
        } else {
            // Mangle namespaced classes: 'Ztal_Form' => 'Ztal\Form'.
            $className = str_replace('_', '\\', $legacyClassName);
        }

        // Use the normal autoloader for the actual loading.
        if (! static::autoload($className)) {
            return false;
        }

        // Alias the actual class to the legacy name that was requested.
        class_alias($className, $legacyClassName);

        return true;
    }

    /**
     * Standard PSR-0 autoloader.
     *
     * Adapted from https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
     *
     * @param string $className Class name to load.
     *
     * @return bool
     */
    static public function autoload($className)
    {
        $className = ltrim($className, '\\');

        if (substr($className, 0, 5) != 'Ztal\\') {
            // Don't bother attempting to load any classes but ours.
            return false;
        } else {
            // Remove the leading 'Ztal\' now, since we're *in* that directory.
            $className = substr($className, 5);
        }

        // Start off the class path.
        $classPath  = __DIR__ . DIRECTORY_SEPARATOR;

        // Detect and append any namespace parts.
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $classPath .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        // Finally append the class name.
        $classPath .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (! file_exists($classPath)) {
            return false;
        }

        include_once $classPath;
        return true;
    }
}
