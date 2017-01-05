<?php
/**
 * Ztal Mail.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

namespace Ztal;

/**
 * Ztal Mail.
 *
 * Provides template support for emails.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Mail extends \Zend_Mail
{
    /**
     * The view object used to render the email content.
     *
     * @var Ztal_Tal_View
     */
    public $view = null;

    /**
     * State that layout was in before we changed it.
     *
     * @var bool
     */
    protected $_layoutWasEnabled;


    /**
     * Generate a macro launch stub to render the correct user template.
     *
     * Emails can have plain and html parts and these are held in a single
     * template file by making them macros with names of 'plain' and 'html'.
     * In order to render the correct macro, this src string is used.
     *
     * @return array
     */
    protected function _template()
    {
        $src = '<tal:block
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:tal="http://xml.zope.org/namespaces/tal"
	xmlns:metal="http://xml.zope.org/namespaces/metal"
	xmlns:i18n="http://xml.zope.org/namespaces/i18n"
	xmlns:phptal="http://phptal.org/ns/phptal"
>
	<tal:block metal:use-macro="${ztalMailMacro}" />
</tal:block>';
        return array('src' => $src, 'name' => __FILE__);
    }


    /**
     * Calculate the path for the email template.
     *
     * @param string $template The template name.
     *
     * @return string
     */
    protected function _calculateTemplatePath($template)
    {
        return '../emails/' . $template . '.email';
    }


    /**
     * Constructor.
     *
     * @param string $charset The charset to set for the email content.
     */
    public function __construct($charset = 'iso-8859-1')
    {
        parent::__construct($charset);

        // We need the view ivar immediately, since users of this class may need
        // to set view variables on it before calling the setBody* methods.
        $this->_createView();
    }

    /**
     * Set the plaintext body of the email to the output from the named template.
     *
     * @param string $template The name of the template.
     * @param string $charset  The charset to use for the content.
     * @param int    $encoding The encoding to use for the content.
     *
     * @return Mail
     */
    public function setBodyTextFromTemplate($template, $charset = null,
        $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE
    ) {
        $this->_setUpLayout();
        $this->view->ztalMailMacro = $this->_calculateTemplatePath($template). '/plain';
        $result = $this->setBodyText($this->view->render($this->_template()), $charset, $encoding);
        $this->_revertLayout();
        return $result;
    }


    /**
     * Set the html body of the email to the output from the named template.
     *
     * @param string $template The name of the template.
     * @param string $charset  The charset to use for the content.
     * @param int    $encoding The encoding to use for the content.
     *
     * @return Mail
     */
    public function setBodyHtmlFromTemplate($template, $charset = null,
        $encoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE
    ) {
        $this->_setUpLayout();
        $this->view->ztalMailMacro = $this->_calculateTemplatePath($template) . '/html';
        $result = $this->setBodyHtml($this->view->render($this->_template()), $charset, $encoding);
        $this->_revertLayout();
        return $result;
    }

    /**
     * Create the view, and store its layout state.
     *
     * @return void
     */
    protected function _createView()
    {
        if (! \Zend_Registry::isRegistered('Ztal_View')) {
            throw new \Exception('No available Ztal View');
        }

        $this->view = clone \Zend_Registry::get('Ztal_View');

        // Remember the state of layout so we can reinstate it after rendering.
        $this->_layoutWasEnabled = $this->view->layout()->isEnabled();
    }

    /**
     * Set up the layout and view ready for rendering.
     *
     * @return void
     */
    protected function _setUpLayout()
    {
        $this->view->layout()->disableLayout();
        $this->view->setCompressWhitespace(true);
    }

    /**
     * Revert the layout back to its previous state.
     *
     * @return void
     */
    protected function _revertLayout()
    {
        if ($this->_layoutWasEnabled) {
            $this->view->layout()->enableLayout();
        }
    }
}