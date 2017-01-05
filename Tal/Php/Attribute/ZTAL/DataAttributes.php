<?php
/**
 * PHPTAL templating engine extensions.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Mike Holloway <mholloway@names.co.uk>
 * @copyright 2009-2013 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Tal\Php\Attribute\ZTAL;

/**
 * Takes a Zend_Form element and builds an attribute string of html5 data attributes.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Mike Holloway <mholloway@names.co.uk>
 */
class DataAttributes extends \PHPTAL_Php_Attribute
{

    /**
     * Attach html5 data attributes to element.
     *
     * Called before generating the compiled php for an attribute.
     *
     * Example (where 'element' is a Zend_Form_Element):
     *
     * <input ztal:data-attributes="element" />
     *
     * Within a Zend_Form implementation:
     *
     * $this->addElement('text', 'example', array(
     *         'data' => array(
     *             'hike' => 'foo',
     *             'bar' => '1337',
     *         )
     * ));
     *
     * @param PHPTAL_Php_CodeWriter $codewriter The code writer.
     *
     * @return void
     */
    public function before(\PHPTAL_Php_CodeWriter $codewriter)
    {
        $args = explode(',', $this->expression);

        $zendFormElement = $codewriter->evaluateExpression(trim($args[0]));
        $dataAttributes = $codewriter->createTempVariable();
        $tmp = $codewriter->createTempVariable();

        $assignment = $tmp . ' = ' . $zendFormElement . ';' . PHP_EOL;
        $attributes = '$attributes = ' . $tmp . '->getAttribs();';

        if (count($args) > 1) {
            $option = $codewriter->evaluateExpression(trim($args[1]));
            $optionVar = $codewriter->createTempVariable();

            $assignment .= $optionVar . ' = ' . $option . ';' . PHP_EOL;
            $attributes = '$attributes = ' . $tmp . '->getAttribsForOption(' . $optionVar . ');';
        }

        // PHPTAL changed the method signature for phptal_escape in v1.2.3.
        if (defined('PHPTAL_VERSION') && version_compare(PHPTAL_VERSION, '1.2.3', '>=')) {
            $escapeFunctionCall = 'phptal_escape($value, \'UTF-8\')';
        } else {
            $escapeFunctionCall = 'phptal_escape($value)';
        }

        /**
         * Compiled code will loop through a Zend_Form_Element attributes
         * looking for the 'data' key, and assign it to a known temporary
         * variable.
         */
        $source = '
		' . $assignment . '
		if ((is_object(' . $tmp . ')
			&& ' . $tmp . ' instanceof Zend_Form_Element)
		) {
			' . $attributes . PHP_EOL . '
			' . $dataAttributes . ' = " ";

			if (isset($attributes["data"]) && is_array($attributes["data"])) {
				foreach ($attributes["data"] as $key => $value) {
					' . $dataAttributes . ' .= "data-{$key}=\"" . ' . $escapeFunctionCall . ' . "\" ";
				}
				' . $dataAttributes . ' = rtrim(' . $dataAttributes . ');
			}
		}';

        // persist the code for compilation
        $codewriter->pushCode($source);

        // get the current DOM element to pull in the attributes
        $this->phpelement
            ->getOrCreateAttributeNode('ztal:data-attributes')
            ->overwriteFullWithVariable($dataAttributes);
    }

    /**
     * Called after generating generating the compiled php for an attribute.
     *
     * @param PHPTAL_Php_CodeWriter $codewriter The code writer class.
     *
     * @return void
     */
    public function after(\PHPTAL_Php_CodeWriter $codewriter)
    {
    }
}