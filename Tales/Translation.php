<?php
/**
 * Tales namespace handler to allow definition of plurals in a translation.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Tales namespace handler to allow definition of plurals in a translation.
 *
 * This class should never be subclassed. It is simply a container for the various tales routines.
 * Creates a namespace for the tales extensions by clustering them as static methods on the class.
 *
 * @category Namesco
 * @package  PHPTal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
final class Ztal_Tales_Translation implements PHPTAL_Tales
{

	/**
	 * Tal extension to allow string casing.
	 *
	 * Example use within template:
	 * <span i18n:translate="ZTranslationTales.plural:string:singularKey,string:pluralKey,countVariable />
	 *
	 * @param string $src     The original string from the source template.
	 * @param bool   $nothrow Whether to throw an exception on error or not.
	 *
	 * @return string
	 */

	public static function plural($src, $nothrow)
	{
		$parts = explode(',', $src);
		$count = array_pop($parts);
		$outputParts = array();
		foreach ($parts as $currentPart) {
			$outputParts[] = str_replace("'", '', phptal_tale($currentPart, $nothrow));
		}
		return 'array(\'pluralKeys\'=>array(\'' . implode('\',\'', $outputParts) . '\'), \'count\'=>'
			. phptal_tales($count, $nothrow) . ', \'ctx\'=>$ctx)';
	}
}