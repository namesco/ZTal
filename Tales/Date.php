<?php
/**
 * PHPTal Tale modifiers: date utilities.
 *
 * @copyright 2022 Namesco Limited
 */

namespace Ztal\Tales;

/**
 * PHPTal Tale modifiers: date utilities.
 *
 * Useful date utility routines within a template.
 */
final class Date implements \PHPTAL_Tales
{
	/**
	 * Current timestamp.
	 *
	 * @var integer
	 */
	protected static int $_currentTs;

	/**
	 * Tal extension to handle a "between" date comparison (fromDate <-> now <-> toDate).
	 *
	 * Note that the exact time for a given date is midnight (the very start of the day), so the date 2022-02-25 is
	 * actually 2022-02-25 00:00:00. Date ranges should take this into account
	 *
	 * Example use within template:
	 *
	 * <div tal:condition="Ztal\Tales\Date.between:string:yyyy-mm-dd,string:yyyy-mm-dd">
	 * 	   date-conditional-content
	 * </div>
	 *
	 * @param string $src     The original template string
	 * @param bool   $nothrow Whether to throw an exception on error
	 *
	 * @return string
	 */
	public static function between(string $src, bool $nothrow): string
	{
		$break = strpos($src, ',');
		$from = substr($src, 0, $break);
		$to = substr($src, $break + 1);

		if (empty($from) || empty($to)) {
			return phptal_tale('NULL', $nothrow);
		}

		return  '(\Ztal\Tales\Date::betweenHelper('. phptal_tale($from, $nothrow) . ', ' . phptal_tale($to, $nothrow) . ')'
			. ' ? '
			. ' : ' . phptal_tale('NULL', $nothrow)
			. ')';
	}

	/**
	 * Helper method to be called from TAL template to return between check result
	 *
	 * @param string $from The from date (yyyy-mm-dd)
	 * @param string $to   The to date (yyyy-mm-dd)
	 *
	 * @return bool
	 */
	public static function betweenHelper(string $from, string $to): bool
	{
		try {
			$from = new \DateTime($from);
		} catch (\Exception $e) {
			throw new \Exception('Could not parse "from" date, please use a yyyy-mm-dd format string');
		}

		try {
			$to = new \DateTime($to);
		} catch (\Exception $e) {
			throw new \Exception('Could not parse "to" date, please use a yyyy-mm-dd format string');
		}

		return self::_getCurrentTs() >= $from->getTimestamp() && self::_getCurrentTs() < $to->getTimestamp();
	}

	/**
	 * Tal extension to handle an "before" date comparison (now -> date).
	 *
	 * Note that the exact time for a given date is midnight (the very start of the day).
	 *
	 * Example use within template:
	 *
	 * <div tal:condition="Ztal\Tales\Date.before:string:yyyy-mm-dd">
	 * 	   date-conditional-content
	 * </div>
	 *
	 * @param string $src     The original template string
	 * @param bool   $nothrow Whether to throw an exception on error
	 *
	 * @return string
	 */
	public static function before(string $src, bool $nothrow): string
	{
		if (empty($src)) {
			return phptal_tale('NULL', $nothrow);
		}

		return  '(\Ztal\Tales\Date::beforeHelper('. phptal_tale($src, $nothrow) . ')'
			. ' ? '
			. ' : ' . phptal_tale('NULL', $nothrow)
			. ')';
	}

	/**
	 * Helper method to be called from TAL template to return before check result
	 *
	 * @param string $date A date string in yyyy-mm-dd format
	 *
	 * @return bool
	 */
	public static function beforeHelper(string $date): bool
	{
		try {
			$date = new \DateTime($date);
		} catch (\Exception $e) {
			throw new \Exception('Could not parse "before" date, please use a yyyy-mm-dd format string');
		}

		return self::_getCurrentTs() < $date->getTimestamp();
	}

	/**
	 * Tal extension to handle an "after" date comparison (date -> now).
	 *
	 * Note that the exact time for a given date is midnight (the very start of the day).
	 *
	 * Example use within template:
	 *
	 * <div tal:condition="Ztal\Tales\Date.after:string:yyyy-mm-dd">
	 * 	   date-conditional-content
	 * </div>
	 *
	 * @param string $src     The original template string
	 * @param bool   $nothrow Whether to throw an exception on error
	 *
	 * @return string
	 */
	public static function after(string $src, bool $nothrow): string
	{
		if (empty($src)) {
			return phptal_tale('NULL', $nothrow);
		}

		return  '(\Ztal\Tales\Date::afterHelper('. phptal_tale($src, $nothrow) . ')'
			. ' ? '
			. ' : ' . phptal_tale('NULL', $nothrow)
			. ')';
	}

	/**
	 * Helper method to be called from TAL template to return after check result
	 *
	 * @param string $date A date string in yyyy-mm-dd format
	 *
	 * @return bool
	 */
	public static function afterHelper(string $date): bool
	{
		try {
			$date = new \DateTime($date);
		} catch (\Exception $e) {
			throw new \Exception('Could not parse "after" date, please use a yyyy-mm-dd format string');
		}

		return self::_getCurrentTs() > $date->getTimestamp();
	}

	/**
	 * Lazy getter for current timestamp.
	 *
	 * @return integer
	 */
	protected static function _getCurrentTs(): int
	{
		if (empty(self::$_currentTs)) {
			self::$_currentTs = time();
		}

		return self::$_currentTs;
	}
}
