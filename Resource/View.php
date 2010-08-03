<?php
/**
 * PHPTal view subclass resource loader to provide PHPTal support in Zend.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

require_once 'PHPTAL.php';
require_once 'PHPTAL/PreFilter.php';
require_once 'PHPTAL/PreFilter/StripComments.php';

/**
 * Overrides the default Zend View to provide Tal templating support through PHPTal
 *
 * Configurable options available through application.ini on the bootstrap:
 * globalTemplatesDirectory[] - locations to look for additional templates
 *						(other than in application/layouts/scripts and [modules]/views/scripts)
 * customModifiersDirectory[] - directories to scan and load php files from in order to bring in custom
 *						modifiers and other code
 * encoding - the default encoding for template files (defaults to UTF-8)
 * cacheDirectory - the directory to use for caching compiled Tal templates
 *						(defaults to the systme tmp folder - usually /tmp/)
 * cachePurgeMode - sets whether to purge the cache after rendering (defaults to false)
 * highlightFailedTranslations - if a translator is installed, set whether failed transaction keys
 *						show up with a prepended '**'
 */

/**
 * Zend View subclass that replaces normal Zend View functionality with PHPTal through the resource loader.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Resource_View extends Zend_Application_Resource_ResourceAbstract
{

	/**
	 * The PHPTal View object itself.
	 *
	 * @var Ztal_Tal_View
	 */
	protected $_view;
	
	/**
	 * The output encoding (not sure it anything other than utf8 will work universally).
	 *
	 * @var string
	 */
	protected $_encoding;
	
	/**
	 * Whether to delete all generated templates before rendering.
	 *
	 * @var bool
	 */
	protected $_cachePurgeMode;
	
	/**
	 * The directory where cached templated are stored.
	 *
	 * @var string
	 */
	protected $_cacheDirectory;
	
	/**
	 * Whether to mark untranslated strings with a '**' prefix.
	 *
	 * @var bool
	 */
	protected $_highlightFailedTranslations;



	/**
	 * Setup the PHPTal view object and set it as the view object used by Zend.
	 *
	 * @return Ztal_Tal_View
	 */
	
	public function init()
	{
		//configure the tal object based on zend options
		$options = $this->getOptions();
				
		$phptal = new PHPTAL();
		
		// We create an instance of our view wrapper and configure it
		// It extends Zend_View so we can configure it the same way
		$this->_view = new Ztal_Tal_View($options);
		$this->_view->setEngine($phptal);
		
		if (Zend_Registry::isRegistered('Zend_Translate')) {
			//setup the translation facilities in PHPTal
			$translator = new Ztal_Tal_ZendTranslateTranslator();		
			$phptal->setTranslator($translator);
			$this->setHighlightFailedTranslations($this->_highlightFailedTranslations);
		}
		

		// We configure the view renderer in order to use our PHPTAL view
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setViewSuffix('xhtml');
		$this->_view->layout()->setViewSuffix('xhtml');
		$viewRenderer->setView($this->_view);
		
		
		//set a default encoding
		if (! isset($this->_encoding)) {
			$this->_encoding = 'UTF-8';
		}
		$this->setEncoding($this->_encoding);
		
		
		//change the compiled code destination if set in the config
		if (! is_null($this->_cacheDirectory)) {
			$this->setCacheDirectory($this->_cacheDirectory);
		}
		
		
		//configure the caching mode
		if (!isset($this->_cachePurgeMode)) {
			$this->_cachePurgeMode = false;
		}
		$this->setCachePurgeMode($this->_cachePurgeMode);

		//set the template repository directories
		$templateDirectories = array(SITE_ROOT_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
			. 'layouts' . DIRECTORY_SEPARATOR . 'scripts');
			
		$this->addTemplateRepositoryPath($templateDirectories);
		
		if ($options != null && array_key_exists('globalTemplatesDirectory', $options)) {
			if (is_array($options['globalTemplatesDirectory'])) {
				foreach ($options['globalTemplatesDirectory'] as $currentDirectory) {
					$this->addTemplateRepositoryPath($currentDirectory);
				}
			} else {
				$this->addTemplateRepositoryPath($options['globalTemplatesDirectory']);
			}
		}

		// Add ZTal's macro repository as a final default.
		$this->addTemplateRepositoryPath(
			realpath(
				dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
				DIRECTORY_SEPARATOR . 'Macros'));
		
		//load in all php files that exist in the custom modifiers directory
		if ($options != null && array_key_exists('customModifiersDirectory', $options)) {
			if (is_array($options['customModifiersDirectory'])) {
				foreach ($options['customModifiersDirectory'] as $currentPath) {
					$this->addCustomModifiersPath($currentPath);
				}
			} elseif (is_string($options['customModifiersDirectory']) && $options['customModifiersDirectory'] != '') {
				$this->addCustomModifiersPath($options['customModifiersDirectory']);
			}
		}

		// Add ZTal's macro repository as a final default.
		$this->addCustomModifiersPath(
			realpath(
				dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
				DIRECTORY_SEPARATOR . 'Tales'));

		return $this->_view;
	}


	/**
	 * Returns the view object.
	 *
	 * @return Ztal_Tal_View
	 */	
	public function getView()
	{
		return $this->_view;
	}
	

	/**
	 * Load in all php files in the specified directory.
	 *
	 * @param string $customModifiersPath Path to scan for php files to load.
	 *
	 * @return Ztal_Resource_View
	 */		
	public function addCustomModifiersPath($customModifiersPath)
	{
		if (is_dir($customModifiersPath)) {
			foreach (new DirectoryIterator($customModifiersPath) as $modifierFile) {
				if ($modifierFile->isDot()) {
					continue;
				}
				if ($modifierFile->isDir()) {
					$this->addCustomModifiersPath($modifierFile->getPathname());
					continue;
				} else {
					if (!preg_match('/^[^.].+\.php$/', $modifierFile->getFilename())) {
						continue;
					}
					include_once $modifierFile->getPathname();
				}
			}
		}
		return $this;
	}
	
	
	/**
	 * Add to or overwrite the list of paths to check for loading in templates.
	 *
	 * @param string $path Path to scan for templates to load.
	 *
	 * @return Ztal_Resource_View
	 */		
	public function addTemplateRepositoryPath($path)
	{
		$this->_view->addTemplateRepositoryPath($path);
		return $this;
	}
	
	
	/**
	 * Set the encoding to use in generating output.
	 *
	 * @param string $encoding The encoding to use.
	 *
	 * @return Ztal_Resource_View
	 */		
	public function setEncoding($encoding)
	{
		$this->_encoding = $encoding;
		if (! is_null($this->_view)) {
			$this->_view->setEncoding($encoding);
			$this->_view->setEngineEncoding($encoding);
		}
		return $this;
	}
	
	
	/**
	 * Set the path to use for storing the templates.
	 *
	 * @param string $path Path to use.
	 *
	 * @return Ztal_Resource_View
	 */		
	public function setCacheDirectory($path)
	{
		$this->_cacheDirectory = realpath($path);
		if (! is_null($this->_view)) {
			$this->_view->setCacheDirectory($path);
		}
		return $this;
	}
	
	
	/**
	 * Set whether to delete all generated templates before rendering the current request.
	 *
	 * @param bool $mode Whether to delete cached templates.
	 *
	 * @return Ztal_Resource_View
	 */		
	public function setCachePurgeMode($mode)
	{
		$this->_cachePurgeMode = ($mode == 1);
		if (! is_null($this->_view)) {
			$this->_view->setCachePurgeMode($mode);	
		}
		return $this;
	}
	
	/**
	 * Set whether to mark untranslated strings with a '**' prefix.
	 *
	 * @param bool $flag Whether to mark untralslated strings.
	 *
	 * @return Ztal_Resource_View
	 */		
	public function setHighlightFailedTranslations($flag)
	{
		$this->_highlightFailedTranslations = $flag;
		if (! is_null($this->_view)) {
			$this->_view->setHighlightFailedTranslations($flag);
		}
		return $this;		
	}
}