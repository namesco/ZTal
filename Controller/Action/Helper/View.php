<?php
/**
 * PHPTal Action helper.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Handles template repository and translation domain mapping just before rendering a template.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Controller_Action_Helper_View extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Maps translation domain to controller and sets up the template repository for the module.
	 *
	 * @return void
	 */			
	public function preDispatch()
	{
		if (!$this->_actionController->view instanceof Ztal_Tal_View) {
			return;
		}
		
		//add a path to search for templates based on the current module 
		$module = $this->_actionController->getRequest()->getModuleName();
		$phptal = $this->_actionController->view->getEngine();
		$phptal->setTemplateRepository(
			SITE_ROOT_PATH . DIRECTORY_SEPARATOR . 'application' .
			DIRECTORY_SEPARATOR .
			($module != '' ? 'modules' . DIRECTORY_SEPARATOR . $module : '') .
			DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts');

		// If the module name is 'default', then the application may not
		// actually be using modules, so we need to also look in the views
		// directory in the application directory.
		$phptal->setTemplateRepository(
			SITE_ROOT_PATH . DIRECTORY_SEPARATOR . 'application' .
			DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts');

			
		$translator = $phptal->getTranslator();
		if (is_object($translator)) {
			$controllerName = $this->_actionController->getRequest()->getControllerName();
			$translator->useDomain($controllerName);
		}
	}
}