<?php
/**
 * PHPTal Tale Modifiers.
 *
 * A collection of extensions to PHPTal that provide useful additional variable
 * handling routines within a form.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Alex Mace <amace@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal\Tales;

/**
 * Form Tale Modifiers.
 *
 * Creates a namespace for the tales extensions by clustering them as static
 * methods on the class. This class should never be subclassed. It is simply a
 * container for the various tales routines.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Alex Mace <amace@names.co.uk>
 */
final class Form implements \PHPTAL_Tales
{
    /**
     * Figures out the simple element type from the one passed in.
     *
     * @param string $type The type to check.
     *
     * @return string
     */
    static public function calculateType($type)
    {
        // This is done in two steps using the $nameParts intermediate variable
        // because it causes a strict error if something other than a defined
        // variable reference is passed to array_pop.
        $nameParts = explode('_', $type);
        $type = strtolower(array_pop($nameParts));

        // Switch multicheckbox to be just "checkbox".
        if ($type == 'multicheckbox') {
            $type = 'checkbox';
        }

        return $type;
    }

    /**
     * Gets the specified attribute from a form element based on the name.
     *
     * Example used within template:
     * <input tal:attributes="maxlength Ztal\Tales\Form.getAttrib:element,string:maxlength" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function getAttrib($src, $nothrow)
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
        return '(' . phptal_tale($a, $nothrow) . '->getAttrib('
         . phptal_tale($b, $nothrow) . ') != null ? '
         . phptal_tale($a, $nothrow) . '->getAttrib('
         . phptal_tale($b, $nothrow) . ') : '
         . phptal_tale($rest, $nothrow) . ')';
    }

    /**
     * Gets the element from a form based on the name.
     *
     * Example used within template:
     * <tal:block tal:define="element Ztal\Tales\Form.getElement:element,string:name" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function getElement($src, $nothrow)
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
        return '(' . phptal_tale($a, $nothrow) . '->getElement('
         . phptal_tale($b, $nothrow) . ') != null ? '
         . phptal_tale($a, $nothrow) . '->getElement('
         . phptal_tale($b, $nothrow) . ') : '
         . phptal_tale($rest, $nothrow) . ')';
    }


    /**
     * Gets the errors for a named element.
     *
     * Example used within template:
     * <tal:block tal:define="element Ztal\Tales\Form.getErrors:form,elementName" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function getErrors($src, $nothrow)
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

        return '((count(\Ztal\Tales\Form::mergeErrors(' . phptal_tale($a, $nothrow) . ', '
         . phptal_tale($b, $nothrow) . ')) > 0) ? \Ztal\Tales\Form::mergeErrors('
         . phptal_tale($a, $nothrow) . ', ' . phptal_tale($b, $nothrow) . ') : '
         . phptal_tale($rest, $nothrow) . ' )';
    }

    /**
     * Helper function to merge different types of error.
     *
     * @param \Zend_Form $form    The form.
     * @param string     $element The name of the element.
     *
     * @return array
     */
    static public function mergeErrors($form, $elementName)
    {
        $elementErrors = $form->getErrors($elementName);
        $element = $form->getElement($elementName);
        if ($element && method_exists($element, 'getErrorMessages')) {
            $elementCustomErrors = $element->getErrorMessages();
        }
        if (!is_array($elementErrors)) {
            $elementErrors = array();
        }
        if (!is_array($elementCustomErrors)) {
            $elementCustomErrors = array();
        }
        return array_merge($elementErrors, $elementCustomErrors);
    }

    /**
     * Tal extension to determine the input field type of a variable.
     *
     * Example use within template:
     * <input tal:attributes="type Ztal\Tales\Form.inputType:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function inputType($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow) . '->getType())';
    }

    /**
     * Tal to determine whether or not the current element is a display group.
     *
     * Example use within template:
     * <fieldset tal:condition="Ztal\Tales\Form.isDisplayGroup:element">
     * </fieldset>
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isDisplayGroup($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return phptal_tale($src, $nothrow) . ' instanceof Zend_Form_DisplayGroup';
    }

    /**
     * Is the current element a standard element?.
     *
     * Tal extension to determine whether or not the current element is a
     * standard form element like input, select, etc.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isFormElement:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isFormElement($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return '((' . phptal_tale($src, $nothrow)
         . ' instanceof Zend_Form_Element) && (Ztal\Tales\Form::calculateType('
         . phptal_tale($src, $nothrow) . '->getType()) != "hidden"))';
    }

    /**
     * Is the current element a hidden element?.
     *
     * Tal extension to determine whether or not the current element is a
     * hidden form element.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isHiddenElement:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isHiddenElement($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return '((' . phptal_tale($src, $nothrow)
         . ' instanceof Zend_Form_Element) && (Ztal\Tales\Form::calculateType('
         . phptal_tale($src, $nothrow) . '->getType()) == "hidden"))';
    }


    /**
     * Tal to determine whether or not the current element is a button input.
     *
     * Example use within template:
     * <button tal:condition="Ztal\Tales\Form.isButton:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isButton($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow) . "->getType()) == 'button'";
    }


    /**
     * Tal to determine whether or not the current element is an image captcha.
     *
     * Example use within template:
     * <button tal:condition="Ztal\Tales\Form.isImageCaptcha:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isImageCaptcha($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return '(Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'captcha' && method_exists("
          . phptal_tale($src, $nothrow) . ', "getImgUrl"))';
    }


    /**
     * Tal to determine whether or not the current element is a captcha input.
     *
     * Example use within template:
     * <button tal:condition="Ztal\Tales\Form.isCaptcha:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isCaptcha($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return '(Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'captcha' && !method_exists("
          . phptal_tale($src, $nothrow) . ', "getImgUrl"))';
    }

    /**
     * When used in conjunction with a known select option, detect if element should be represented as optgroup.
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isOptGroup($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return '(true === is_array(' . phptal_tale($src, $nothrow) . '))';
    }

    /**
     * Should the current option in a multi check box be checked or not.
     *
     * Determines if the current option for a multi check box should be checked
     * or not. If the value of this option appears in the values of the element
     * (rather than options, options are potential values) then it should be
     * checked. First argument to the tale is the element and second is the
     * current option.
     *
     * Example used within template:
     * <tal:block tal:define="checked Ztal\Tales\Form.isChecked:element,repeat/option/key" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isChecked($src, $nothrow)
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
        return '(is_array(' . phptal_tale($a, $nothrow) . '->getValue()) ? ' .
         'in_array(' . phptal_tale($b, $nothrow) . ', ' .
         phptal_tale($a, $nothrow) . '->getValue()) : false)';
    }

    /**
     * Checks whether an element is disabled or not.
     *
     * Example used within template:
     * <tal:block tal:attributes="disabled Ztal\Tales\Form.isDisabled:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isDisabled($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return phptal_tale($src, $nothrow) . '->getAttrib("disabled") ? true : false';
    }


    /**
     * Checks whether an element is required or not.
     *
     * Example used within template:
     * <tal:block tal:define="required Ztal\Tales\Form.isRequired:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isRequired($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return phptal_tale($src, $nothrow) . '->isRequired()';
    }


    /**
     * Checks whether an element is readonly or not.
     *
     * Example used within template:
     * <tal:block tal:define="readonly Ztal\Tales\Form.isReadOnly:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isReadOnly($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return '(bool) ' . phptal_tale($src, $nothrow) . '->getAttrib(\'readonly\')';
    }


    /**
     * Tal extension to determine whether or not the current element is an input.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isInput:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isInput($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'in_array(Ztal\Tales\Form::calculateType('
         . phptal_tale($src, $nothrow) . "->getType()), "
         . "array('text', 'hidden', 'password', 'date', 'email', 'file'))";
    }


    /**
     * Tal to determine whether or not the current element is a password input.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isPassword:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    public static function isPassword($src, $nothrow)
    {

        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'in_array(Ztal\Tales\Form::calculateType('
         . phptal_tale($src, $nothrow) . "->getType()), "
         . "array('password'))";

    }

    /**
     * Tal to determine whether or not the current element is a multi checkbox.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isMultiCheckbox:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isMultiCheckbox($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        // If an input has multiple options, then it will have the function
        // "getMultiOptions". However, selects also have that function so we
        // have to check that this isn't a select.
        return 'method_exists(' . phptal_tale($src, $nothrow)
         . ", 'getMultiOptions') && Ztal\Tales\Form::calculateType("
         . phptal_tale($src, $nothrow) . "->getType()) == 'checkbox'";
    }


    /**
     * Tal to determine whether or not the current element is a radio element.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isRadio:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isRadio($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'radio'";
    }


    /**
     * Tal to determine whether or not the current element is a checkbox.
     *
     * Example use within template:
     * <input tal:condition="Ztal\Tales\Form.isCheckbox:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isCheckbox($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'checkbox'";
    }


    /**
     * Tal extension to determine whether or not the current element is a select.
     *
     * Example use within template:
     * <select tal:condition="Ztal\Tales\Form.isSelect:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isSelect($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'select'";
    }


    /**
     * Tal to determine whether or not the current element is a multi select.
     *
     * Example use within template:
     * <select tal:condition="Ztal\Tales\Form.isSelect:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isMultiSelect($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'multiselect'";
    }


    /**
     * Tal to determine whether or not the current element is a textarea input.
     *
     * Example use within template:
     * <textarea tal:condition="Ztal\Tales\Form.isTextarea:element" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function isTextarea($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }

        return 'Ztal\Tales\Form::calculateType(' . phptal_tale($src, $nothrow)
         . "->getType()) == 'textarea'";
    }


    /**
     * Tal to determine whether the element should have a label displayed before it.
     *
     * Example use within template:
     * <label tal:condition="Ztal\Tales\Form.showLabelBefore:element"
     *        i18n:translate="element/getLabel" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function showLabelBefore($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return 'in_array(Ztal\Tales\Form::calculateType('
         . phptal_tale($src, $nothrow) . '->getType()), '
         . "array('date', 'email', 'password', 'file', 'select', 'multiselect', 'text', 'textarea')) && "
         . phptal_tale($src, $nothrow) . '->getLabel()';
    }


    /**
     * Tal to determine whether the element should have a label displayed after it.
     *
     * Example use within template:
     * <label tal:condition="Ztal\Tales\Form.showLabelAfter:element"
     *        i18n:translate="element/getLabel" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function showLabelAfter($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break !== false) {
            $src = substr($src, 0, $break);
        }
        return 'in_array(Ztal\Tales\Form::calculateType('
         . phptal_tale($src, $nothrow) . '->getType()), '
         . "array('checkbox', 'radio')) && "
         . phptal_tale($src, $nothrow) . '->getLabel()';
    }


    /**
     * Tal extension to inject slot content into a variable.
     *
     * Slot names cannot (currently) be dynamic in phptal so this tale
     * allows us to grab the content of a slot with a dynamic name and
     * assign it to a variable which we can then output.
     *
     * Example use within template:
     * <tal:block tal:define="slotContent Ztal\Tales\Form.getSlotContent:slotName" />
     *
     * @param string $src     The original template string.
     * @param bool   $nothrow Whether to throw an exception on error.
     *
     * @return string
     */
    static public function getSlotContent($src, $nothrow)
    {
        $break = strpos($src, '|');
        if ($break === false) {
            $slotName = $src;
            $notTrue = 'NULL';
        } else {
            $slotName = substr($src, 0, $break);
            $notTrue = substr($src, $break + 1);
        }
        return '($ctx->hasSlot(' . phptal_tale($slotName, $nothrow)
         . ')?$ctx->getSlot(' . phptal_tale($slotName, $nothrow)
         . '):' . phptal_tale($notTrue, $nothrow) . ')';
    }
}
