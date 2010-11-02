<?php
/**
 * Zend View subclass that handles actual rendering of View content using PHPTal.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Subclass of Zend View that replaces the standard functionality with PHPTal template rendering.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Tal_View extends Zend_View
{

	/**
	 * The PHPTal engine.
	 *
	 * @var PHPTAL
	 */
	protected $_engine = null;

	/**
	 * Whether to flush the template cache before rendering.
	 *
	 * @var bool
	 */
	protected $_purgeCacheBeforeRender = false;

	/**
	 * The Zend_Cache object to use in page render caching operations.
	 *
	 * @var Zend_Cache
	 */
	protected $_zendPageCache = null;

	/**
	 * The page render cache key to use in page render cache operations.
	 *
	 * @var string
	 */
	protected $_zendPageCacheKey = null;

	/**
	 * How long to cache, in seconds, a new page prender.
	 *
	 * @var int
	 */
	protected $_zendPageCacheDuration = null;

	/**
	 * Whether to use a cached page render if one exists.
	 *
	 * @var bool
	 */
	protected $_useCachedVersion = false;

	/**
	 * Whether to cache the result of a page render.
	 *
	 * @var bool
	 */
	protected $_cacheResult = false;

	/**
	 * Whether to turn on the whitespace compression filter.
	 *
	 * @var bool
	 */
	protected $_compressWhitespace = false;

	/**
	 * Constructor.
	 *
	 * @param array $options Configuration options.
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);
		
		$this->setEngine(new PHPTAL());
		
		// configure the encoding
		if (isset($options['encoding']) && $options['encoding'] != '') {
			$this->setEncoding((string)$options['encoding']);
		} else {
			$this->setEncoding('UTF-8');
		}

		// change the compiled code destination if set in the config
		if (isset($options['cacheDirectory']) && $options['cacheDirectory'] != '') {
			$this->setCacheDirectory((string)$options['cacheDirectory']);
		}

		// configure the caching mode
		if (isset($options['cachePurgeMode'])) {
			$this->setCachePurgeMode($options['cachePurgeMode'] == '1');
		}
		
		// configure the whitespace compression mode
		if (isset($options['compressWhitespace'])) {
			$this->setCompressWhitespace($options['compressWhitespace'] == '1');
		}
		
		// set the layout template path
		$this->addTemplateRepositoryPath(Zend_Layout::getMvcInstance()->getLayoutPath());

		// Set the remaining template repository directories;
		if (isset($options['globalTemplatesDirectory'])) {
			$directories = $options['globalTemplatesDirectory'];
			if (!is_array($directories)) {
				$directories = array($directories);
			}
			foreach ($directories as $currentDirectory) {
				$this->addTemplateRepositoryPath($currentDirectory);
			}
		}

		$ztalBasePath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
		
		// Add ZTal's macro repository as a final default.
		$ztalMacroPath = $ztalBasePath . DIRECTORY_SEPARATOR . 'Macros';
		$this->addTemplateRepositoryPath($ztalMacroPath);
		
		
		//load in all php files that exist in the custom modifiers directory
		if (isset($options['customModifiersDirectory'])) {
			$customModifiers = $options['customModifiersDirectory'];
			if (!is_array($customModifiers)) {
				$customModifiers = array($customModifiers);
			}
			foreach ($customModifiers as $currentPath) {
				$this->addCustomModifiersPath($currentPath);
			}
		}

		// Add ZTal's tales repository as a final default.
		$ztalTalesPath = $ztalBasePath . DIRECTORY_SEPARATOR . 'Tales';
		$this->addCustomModifiersPath($ztalTalesPath);
	}
	 
	
	/**
	 * Load in all php files in the specified directory.
	 *
	 * @param string $customModifiersPath Path to scan for php files to load.
	 *
	 * @return void
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
	}
	  
		  

	/**
	 * Changes the current PHPTAL instance.
	 *
	 * @param mixed $tal The engine to use (supplied to the Zend system by the Resource View).
	 *
	 * @return void
	 */
	public function setEngine($tal)
	{
		$this->_engine = $tal;
		$this->_engine->this = $this;
	}

	/**
	 * Returns the current PHPTAL instance.
	 *
	 * @return std_class
	 */
	public function getEngine()
	{
		return $this->_engine;
	}

	
	/**
	 * Configures the Zend_Cache support for capturing render output and retrieving cached pages.
	 *
	 * @param Zend_Cache $cache    The cache instance to use.
	 * @param string     $key      The key to use to reference the page.
	 * @param int        $duration How long to cache the page for.
	 *
	 * @return void
	 */
	public function setZendPageCache($cache, $key, $duration)
	{
		$this->_zendPageCache = $cache;
		$this->_zendPageCacheKey = $key;
		$this->_zendPageCacheDuration = $duration;
	}
	
	
	/**
	 * Use the cached version of a page if it exists (using the cache and key configured in setZendPageCache).
	 *
	 * @return bool Whether a cached version of the page can be used.
	 */
	public function useZendCachedPage()
	{
		$this->_useCachedVersion = false;
		if ($this->_zendPageCache == null || $this->_zendPageCacheKey == null) {
			return false;
		}
		if (!$this->_zendPageCache->test($this->_zendPageCacheKey)) {
			return false;
		}
		$this->_useCachedVersion = true;
		return true;
	}
	

	/**
	 * Cache the render as well as output it. Uses the details configured in setZendPageCache.
	 *
	 * @return bool Whether it will be possible to cache the render.
	 */
	public function zendCache()
	{
		if ($this->_zendPageCache == null
			|| $this->_zendPageCacheKey == null 
			|| $this->_zendPageCacheDuration == null
		) {
			return false;
		}
		$this->_cacheResult = true;
		return true;
	}


	
	/**
	 * Flush the page cache. Uses the details configured in setZendPageCache.
	 *
	 * @return bool Whether it was possible to flush the cache.
	 */
	public function flushZendCache()
	{
		if ($this->_zendPageCache == null) {
			return false;
		}
		return $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('PHPTALPage'));			
	}
	
	
	/**
	 * Changes the cache purge mode.
	 *
	 * @param bool $newValue Whether to delete old template cache files before rendering.
	 *
	 * @return void
	 */	  
	public function setCachePurgeMode($newValue)
	{
		$this->_purgeCacheBeforeRender = $newValue;
	}

	/**
	 * Sets the encoding used in outputting renders.
	 *
	 * @param string $encoding The encoding to use.
	 *
	 * @return void
	 */
	public function setEncoding($encoding)
	{
		parent::setEncoding($encoding);
		$this->_engine->setEncoding(parent::getEncoding());
	}


	/**
	 * Sets whether whitespace compression should be performed.
	 *
	 * @param bool $flag Whether to compress whitespace.
	 *
	 * @return void
	 */
	public function setCompressWhitespace($flag)
	{
		$this->_compressWhitespace = (bool)$flag;
	}


	/**
	 * Gets whether whitespace compression is currently turned on.
	 *
	 * @return bool
	 */
	public function getCompressWhitespace()
	{
		return $this->_compressWhitespace;
	}


	
	/**
	 * Either append or overwrite the paths used to find a template.
	 *
	 * Pass a string to append, pass an array of strings to overwrite.
	 *
	 * @param string|array $path The path / paths to use.
	 *
	 * @return void
	 */
	public function addTemplateRepositoryPath($path)
	{
		$this->_engine->setTemplateRepository($path);
	}

	
	/**
	 * Set the directory used to save generated templates.
	 *
	 * @param string $path The path to use.
	 *
	 * @return void
	 */
	public function setCacheDirectory($path)
	{
		$this->_engine->setPhpCodeDestination($path);	
	}
	

	/**
	 * Whether untranslated strings should be highlighted by prepending '**'.
	 *
	 * @param bool $flag Whether to highlight failed translations.
	 *
	 * @return void
	 */
	public function setHighlightFailedTranslations($flag)
	{
		$translator = $this->_engine->getTranslator();
		if (is_object($translator)) {
			$translator->setHighlightFailedTranslations($flag);
		}
	}

	/**
	 * Returns the cache purge mode.
	 *
	 * @return bool
	 */
	
	public function getCachePurgeMode()
	{
		return $this->_purgeCacheBeforeRender;
	}
	
	/**
	 * Sets a value to the view.
	 *
	 * @param string $key   The member variable to set.
	 * @param mixed  $value The value to set the variable to.
	 *
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->_checkLoaded();
		$this->_engine->set($key, $value);
	}


	/**
	 * Sets which template to render.
	 *
	 * @param string $template The name of the template to render.
	 *
	 * @return bool
	 */
	public function setTemplate($template)
	{
		$this->_checkLoaded();
		return $this->_engine->setTemplate($template);
	}


	/**
	 * Retrieves a value from the view.
	 *
	 * @param string $key The member variable to access.
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		$context = $this->_engine->getContext();
		return $context->$key;
	}
	
	/**
	 * Checks whether a value in the view has been set.
	 *
	 * @param string $key The member variable to check.
	 *
	 * @return bool
	 */
	
	public function __isset($key)
	{
		return isset($this->_engine->getContext()->$key);
	}

	

	/**
	 * Returns PHPTAL output - either from a render or from the cache.
	 *
	 * @param string $template The name of the template to render.
	 *
	 * @return string
	 */
	public function render($template)
	{		
		if ($this->_useCachedVersion && !$this->_cacheResult) {
			$result = $this->_zendPageCache->load($this->_zendPageCacheKey);
			if ($result !== false ) {
				return $result;
			}
		}
		
		//conversion of template names from '-' split to camel-case 
		$templateParts = explode('-', $template);
		$firstPart = array_shift($templateParts);
		foreach ($templateParts as &$currentPart) {
			$currentPart = ucfirst($currentPart);
		}
		$template = $firstPart . implode('', $templateParts);

		$this->_checkLoaded();
		$this->_engine->setTemplate($template);
		$this->productionMode = ('production' == APPLICATION_ENV);
		$this->_engine->set('doctype', $this->doctype());
		$this->_engine->set('headTitle', $this->headTitle());
		$this->_engine->set('headScript', $this->headScript());
		$this->_engine->set('headLink', $this->headLink());
		$this->_engine->set('headMeta', $this->headMeta());
		$this->_engine->set('headStyle', $this->headStyle());

		
		if ($this->_purgeCacheBeforeRender) {
			$cacheFolder = $this->_engine->getPhpCodeDestination();
			if (is_dir($cacheFolder)) {
				foreach (new DirectoryIterator($cacheFolder) as $cacheItem) {
					if (strncmp($cacheItem->getFilename(), 'tpl_', 4) != 0 || $cacheItem->isdir()) {
						continue;
					}
					@unlink($cacheItem->getPathname());
				}
			}
		}
		
		
		// if a layout is being used and nothing has already overloaded the viewContent,
		// register the content as viewContent, otherwise set it to empty
		if (!isset($this->viewContent)) {
			if ($this->getHelperPath('layout') != false && $this->layout()->isEnabled()) {
				$this->_engine->set('viewContent', $this->layout()->content);
			} else {
				$this->viewContent = '';
			}
		}
		
		// Strip html comments and compress un-needed whitespace
		$this->_engine->addPreFilter(new PHPTAL_PreFilter_StripComments());
		
		if ($this->_compressWhitespace == true) {
			$this->_engine->addPreFilter(new PHPTAL_PreFilter_Compress());
		}
		
		try {
			$result = $this->_engine->execute();
			if ($this->_cacheResult
				&& $this->_zendPageCache != null
				&& $this->_zendPageCacheKey != null
				&& $this->_zendPageCacheDuration > 0
			) {
				$this->_zendPageCache->save($result, $this->_zendPageCacheKey,
					array('PHPTALPage'), $this->_zendPageCacheDuration);
					
			}
		} catch(PHPTAL_TemplateException $e) {
			// If the exception is a root PHPTAL_TemplateException
			// rather than a subclass of this exception and xdebug is enabled,
			// it will have already been picked up by xdebug, if enabled, and
			// should be shown like any other php error.
			// Any subclass of PHPTAL_TemplateException can be handled by
			// the phptal internal exception handler as it gives a useful
			// error output
			if (get_class($e) == 'PHPTAL_TemplateException'
				&& function_exists('xdebug_is_enabled')
				&& xdebug_is_enabled()
			) {
				exit();
			}
			throw $e;
		}
		return $result;
	}

	/**
	 * Needed as a subclass of Zend_View but not used.
	 *
	 * @return void
	 */
	protected function _run ()
	{
	}


	/**
	 * Checks that the engine has been correctly created.
	 *
	 * @return void
	 * @throws Zend_View_Exception If the engine is not configured.
	 */
	private function _checkLoaded()
	{
		if ($this->_engine == null) {
			include_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception('PHPTAL is not defined', $this);
		}
	}

}