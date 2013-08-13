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

namespace Ztal\Tal\Ns;

/**
 * Register a new namespace 'Ztal'.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Mike Holloway <mholloway@names.co.uk>
 */
class ZTAL extends \PHPTAL_Namespace
{
	/**
	 * Class contructor.
	 */
	public function __construct()
	{
		parent::__construct('ztal', 'http://names.co.uk/namespaces/ztal');
		$this->addAttribute(new \PHPTAL_NamespaceAttributeSurround('data-attributes', 20));
	}

	/**
	 * Create an instance of a class that can handle processing the supplied attribute.
	 *
	 * @param PHPTAL_NamespaceAttribute $att        The attribute.
	 * @param PHPTAL_Dom_Element        $tag        The element containing the attribute.
	 * @param string                    $expression The attribute expression.
	 *
	 * @return AppLibrary_PHPTAL_Php_Attribute
	 */
	public function createAttributeHandler(\PHPTAL_NamespaceAttribute $att,
		\PHPTAL_Dom_Element $tag, $expression
	) {
		$name = $att->getLocalName();

		// change define-macro to "define macro" and capitalize words
		$name = str_replace(' ', '', ucwords(strtr($name, '-', ' ')));
		$class = 'Ztal_Tal_Php_Attribute_' . strtoupper($this->getPrefix()) . '_' . $name;
		$result = new $class($tag, $expression);
		return $result;
	}
}