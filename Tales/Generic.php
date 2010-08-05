<?php
/**
 * PHPTal Tale Modifiers.
 *
 * A collection of extensions to PHPTal that provide useful additional variable handling routines within a template.
 *
 * @category  Namesco
 * @package   PHPTal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

require_once 'PHPTAL/Tales.php';

/**
 * Creates a namespace for the tales extensions by clustering them as static methods on the class.
 *
 * This class should never be subclassed. It is simply a container for the various tales routines.
 *
 * @category Namesco
 * @package  PHPTal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
	
final class Ztal_Tales_Generic implements PHPTAL_Tales
{

	/**
	 * Tal extension to allow string casing.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.uc:option,variable" />
	 * Options:
	 *		first - uppercase the first letter of the string
	 *		word - uppercase the first letter of each word
	 *		all - uppercase the whole string
	 *		none - force lowercase on the whole string.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function uc($src, $nothrow)
	{
		$break = strpos($src, ',');
		$command = strtolower(substr($src, 0, $break));
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$string = $src;
			$rest = 'NULL';
		} else {
			$string = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		switch ($command) {
				
			case 'word':
				return 'ucword(' . phptal_tale($src, $nothrow) . ')';
				break;
				
			case 'all':
				return 'strtoupper(' . phptal_tale($src, $nothrow) . ')';
				break;
				
			case 'none':
				return 'strtolower(' . phptal_tale($src, $nothrow) . ')';
				break;
			
			case 'first':
			default:
				return 'ucfirst(' . phptal_tale($src, $nothrow) . ')';
				break;
		}
		return phptal_tales($rest, $nothrow);
	}


	/**
	 * Tal extension to allow string replacement.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.strReplace:string,original,replacement" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */

	public static function strReplace($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		$parts = explode(',', $src);
		return 'str_replace(' . phptal_tale($parts[1], $nothrow) . ', ' . phptal_tale($parts[2], $nothrow) 
			. ', ' . phptal_tale($parts[0], $nothrow) . ')';
	}


	/**
	 * Tal extension to build a data structure out of a json string.
	 *
	 * Example use within template: <span tal:define=" myVar Ztal_Tales_Generic.fromJsonString:
	 * {'name':'robert','gender':'male'}" />
	 * Note that single rather than double quotes are used to wrap strings and these are auto-converted.
	 * In order to insert a single quote into the string content, use 2 single quotes together ('').
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */

	public static function fromJsonString($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break === false) {
			$jsonString = $src;
		} else {
			$jsonString = substr($src, 0, $break);
		}

		$jsonString = str_replace(array("'", '""'), array('"',"\'"), $jsonString);
		return 'json_decode(' . phptal_tale($jsonString, $nothrow) . ', true)';
	}



	/**
	 * Tal extension to return the php type of a variable.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.phpType:variable" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */

	public static function phpType($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return 'Ztal_Tales_Generic::runtimeCalculateType(' . phptal_tale($src, $nothrow) . ')';
	}
	
	
	/**
	 * Used by the phpType tal extension, returns a string of the type of the supplied variable.
	 *
	 * Should NEVER be called directly.
	 *
	 * This method is used rather than gettype directly because gettype is not guaranteed to return the
	 * same string values in future. This function can be updated to handle that possibility.
	 *
	 * @param mixed $var The var to calculate the type for.
	 *
	 * @return string
	 */
	public static function runtimeCalculateType($var)
	{
		return gettype($var);
	}


	/**
	 * Tal extension to return the result of a mod b (a%b in php talk).
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.mod:a,b" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function mod($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
		} else {
			$b = substr($src, 0, $break);
		}
		return '(' . phptal_tale($a, $nothrow) . '%' . phptal_tale($b, $nothrow) . ')';
	}



	/**
	 * Tal extension to return true when both arguments are equal.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.equal:a,b" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function equal($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(' . phptal_tale($a, $nothrow) . '==' . phptal_tale($b, $nothrow) . '?1:' 
			. phptal_tales($rest, $nothrow) . ')';
	}



	/**
	 * Tal extension to return the supplied string when the value is true.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.isTrue:variable,string" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isTrue($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$string = $src;
			$notTrue = 'NULL';
		} else {
			$string = substr($src, 0, $break);
			$notTrue = phptal_tales(substr($src, $break + 1), $nothrow);
		}
		return '(' . phptal_tale($variable, $nothrow) . '==true?' . phptal_tale($string, $nothrow)
			. ':' . $notTrue . ')';
	}



	/**
	 * Tal extension to handle Zend_Date objects.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.zendDate:variable,format" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function zendDate($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$format = $src;
		} else {
			$format = substr($src, 0, $break);
		}
		return phptal_tale($variable, $nothrow) . '->toString("' . $format . '")';
	}


	/**
	 * Tal extension to handle formatting of numbers using a supplied Zend_Locale object.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.zendLocaleNumber:variable,localeObject" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string	 
	 */
	public static function zendLocaleNumber($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$locale = $src;
		} else {
			$locale = substr($src, 0, $break);
		}
		return 'Zend_Locale_Format::toNumber((' . phptal_tale($variable, $nothrow) . '==\'\'?0:' 
			. phptal_tale($variable, $nothrow) . '), array(\'locale\' => ' . phptal_tale($locale, $nothrow) . '))';
	}


	/**
	 * Tal extension to handle formatting of numbers using a supplied Zend_Currency object.
	 *
	 * Example use within template: <span tal:content="Ztal_Tales_Generic.zendCurrency:variable,currencyObject" />.
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function zendCurrency($src, $nothrow)
	{
		$break = strpos($src, ',');
		$variable = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$currency = $src;
		} else {
			$currency = substr($src, 0, $break);
		}
		return phptal_tale($currency, $nothrow) . '->toCurrency(' . phptal_tale($variable, $nothrow) . ')';
	}
}