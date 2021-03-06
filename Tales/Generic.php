<?php
/**
 * PHPTal Tale Modifiers.
 *
 * Useful additional variable handling routines within a template.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Tales;

/**
 * Creates a namespace for the tales extensions.
 *
 * This class should never be subclassed. It is simply a container for the
 * various tales routines.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

final class Generic implements \PHPTAL_Tales
{
    /**
     * Tal extension to allow string casing.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.uc:option,variable" />
     * Options:
     *        first - uppercase the first letter of the string
     *        word - uppercase the first letter of each word
     *        all - uppercase the whole string
     *        none - force lowercase on the whole string.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function uc($src, $nothrow)
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
            return 'ucwords(' . phptal_tale($src, $nothrow) . ')';
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
     * Tal extension to convert new lines to <br> and any text to htmlentities.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function toHTML($src, $nothrow)
    {
        return 'nl2br(htmlspecialchars(' . phptal_tales($src, $nothrow) . '))';
    }


    /**
     * Tal extension to allow string replacement.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.strReplace:string,original,replacement" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function strReplace($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        $parts = explode(',', $src);
        return 'str_replace(' . phptal_tale($parts[1], $nothrow) . ', '
         . phptal_tale($parts[2], $nothrow) . ', '
         . phptal_tale($parts[0], $nothrow) . ')';
    }


    /**
     * Tal extension to allow counting of items.
     *
     * Example use within template:
     *  <span class="item" tal:content="Ztal\Tales\Generic.count:array,ticket/posts">1</span>
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @static
     * @return string
     */
    static public function count($src, $nothrow)
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
        case 'string':
            return 'strlen(' . phptal_tale($src, $nothrow) . ')';
          break;

        default:
        case 'array':
            return 'count(' . phptal_tale($src, $nothrow) . ')';
          break;
        }
        return phptal_tales($rest, $nothrow);
    }


    /**
     * Tal extension: Adds ellipsis to strings when it's over a given length.
     *
     * Example use within template:
     *  <td tal:content="Ztal\Tales\Generic.ellipsis:ticket/posts/0/body,string:100" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @static
     * @return string
     */
    static public function ellipsis($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        $parts = explode(',', $src);

        return 'substr(' . phptal_tale($parts[0], $nothrow) . ', 0, '
         . phptal_tale($parts[1], $nothrow) . ') . (strlen('
         . phptal_tale($parts[0], $nothrow) . ') > '
         . phptal_tale($parts[1], $nothrow) . ' ? "..." : "")';
    }


    /**
     * Tal extension to build a data structure out of a json string.
     *
     * Example use within template:
     * <span tal:define=" myVar Ztal\Tales\Generic.fromJsonString:{'name':'robert','gender':'male'}" />
     *
     * Note that single rather than double quotes are used to wrap strings and
     * these are auto-converted. In order to insert a single quote into the
     * string content, use 2 single quotes together ('').
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function fromJsonString($src, $nothrow)
    {
        $jsonString = str_replace(array("'", '""'), array('"',"\'"), $src);
        return 'json_decode(' . phptal_tale($jsonString, $nothrow) . ', true)';
    }


    /**
     * Tal extension to return the php type of a variable.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.phpType:variable" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function phpType($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return 'Ztal\Tales\Generic::runtimeCalculateType('
         . phptal_tale($src, $nothrow) . ')';
    }


    /**
     * Used by the phpType tal, returns the type of the supplied variable.
     *
     * Should NEVER be called directly.
     *
     * This method is used rather than gettype directly because gettype is not
     * guaranteed to return the same string values in future. This function can
     * be updated to handle that possibility.
     *
     * @param mixed $var The var to calculate the type for.
     *
     * @return string
     */
    static public function runtimeCalculateType($var)
    {
        return gettype($var);
    }


    /**
     * Tal extension to return the result of a mod b (a%b in php talk).
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.mod:a,b" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function mod($src, $nothrow)
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
     * Example use within template: <span tal:content="Ztal\Tales\Generic.equal:a,b" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function equal($src, $nothrow)
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
        return '(' . phptal_tale($a, $nothrow) . '==' . phptal_tale($b, $nothrow)
         . '?1:' . phptal_tale($rest, $nothrow) . ')';
    }


    /**
     * Tal extension to return true when the first argument is greater.
     *
     * Example use within template:
     *    <span tal:content="Ztal\Tales\Generic.greaterThan:a,b" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function greaterThan($src, $nothrow)
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
        return '(' . phptal_tale($a, $nothrow) . '>'
         . phptal_tale($b, $nothrow) . ' ? true : false)';
    }


    /**
     * Tal extension to return whether a regex pattern matches a string.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.regexMatch:pattern,subject" />.
     *
     * Note that, per PHPTal convention, if you want to use $ you need to use $$
     * within the pattern string or PHPTal will attempt to convert a var.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    public static function regexMatch($src, $nothrow)
    {
        $break = strpos($src, ',');
        $pattern = substr($src, 0, $break);
        $src = substr($src, $break + 1);
        $break = strpos($src, '|');
        if ($break === false) {
            $subject = $src;
            $rest = 'NULL';
        } else {
            $subject = substr($src, 0, $break);
            $rest = substr($src, $break + 1);
        }

        return '(preg_match("#" . str_replace("#", "##", ' . phptal_tale($pattern, $nothrow) . ') . "#", '
         . phptal_tale($subject, $nothrow) . ') ?: '
         . phptal_tale($rest, $nothrow) . ')';
    }


    /**
     * Tal extension to return true when all parameters are true.
     *
     * Example use within template:
     *    <span tal:content="Ztal\Tales\Generic.allTrue:a,b" />.
     *
     * Note that this tale will accept an unlimited number of parameters.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function allTrue($src, $nothrow)
    {
        $pipePos = strpos($src, '|');
        if ($pipePos !== false) {
            $rest = substr($src, $pipePos);
            $src = substr($src, 0, $pipePos - 1);
        } else {
            $rest = null;
        }

        $parameters = explode(',', $src);

        if ($rest) {
            $output = '(';
        } else {
            $output = '';
        }

        $output .= '(' . phptal_tale(array_pop($parameters), $nothrow);
        foreach ($parameters as $currentParameter) {
            $output .= ' && ' . phptal_tale($currentParameter, $nothrow);
        }
        $output .= ')';

        if ($rest) {
            $output .= '?:' . phptal_tale($rest, $nothrow) . ')';
        }
        return $output;
    }


    /**
     * Tal extension to return the supplied string when the value is true.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.isTrue:variable,string" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isTrue($src, $nothrow)
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
            $notTrue = phptal_tale(substr($src, $break + 1), $nothrow);
        }
        return '(' . phptal_tale($variable, $nothrow) . '==true?'
         . phptal_tale($string, $nothrow) . ':' . $notTrue . ')';
    }

    /**
     * Check if a value is empty.
     *
     * Example use: <tal:block tal:content="Ztal\Tales\Generic.isEmpty:variable,string" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return boolean
     */
    static public function isEmpty($src, $nothrow)
    {
        $break = strpos($src, ',');
        $data = substr($src, 0, $break);
        $src = substr($src, $break + 1);

        $break = strpos($src, '|');
        if (false === $break) {
            $string = $src;
            $notEmpty = 'NULL';
        } else {
            $string = substr($src, 0, $break);
            $notEmpty = phptal_tale(substr($src, $break + 1), $nothrow);
        }

        return '(empty(trim(' . phptal_tale($data, $nothrow) . ')) ? '
         . phptal_tale($string, $nothrow) . ' : ' . $notEmpty . ')';
    }


    /**
     * Tal extension to handle Zend_Date objects.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.zendDate:variable,format" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function zendDate($src, $nothrow)
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
     * Tal to handle formatting of numbers using a supplied Zend_Locale object.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.zendLocaleNumber:variable,localeObject" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function zendLocaleNumber($src, $nothrow)
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
        return 'Zend_Locale_Format::toNumber((' . phptal_tale($variable, $nothrow)
         . '==\'\'?0:' . phptal_tale($variable, $nothrow)
         . '), array(\'locale\' => ' . phptal_tale($locale, $nothrow) . '))';
    }


    /**
     * Tal to handle formatting of numbers using a supplied Zend_Currency object.
     *
     * Example use within template: <span tal:content="Ztal\Tales\Generic.zendCurrency:variable,currencyObject" />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function zendCurrency($src, $nothrow)
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
        return phptal_tale($currency, $nothrow) . '->toCurrency('
         . phptal_tale($variable, $nothrow) . ')';
    }


    /**
     * Formats a number to a certain decimal place.
     *
     * Formats the number to the provided decimal place - that's numberwang!
     * Example use within template:
     *
     * <span
     *   tal:content="Ztal\Tales\Generic.numberFormatDecimal:numberVar,string:2"
     * />.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function numberFormatDecimal($src, $nothrow)
    {
        $break = strpos($src, ',');
        $variable = substr($src, 0, $break);
        $src = substr($src, $break + 1);
        $break = strpos($src, '|');
        if ($break === false) {
            $decimal = $src;
        } else {
            $decimal = substr($src, 0, $break);
        }

        return 'number_format(' . phptal_tale($variable, $nothrow)
         . ', ' . phptal_tale($decimal, $nothrow) . ')';
    }
}
