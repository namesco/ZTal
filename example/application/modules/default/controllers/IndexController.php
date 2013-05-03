<?php
/**
 * Default index controller.
 *
 * Controller to handle requests with no module, / and /index.php requests.
 *
 * @category  Namesco
 * @package   ZtalExample
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Default index controller.
 *
 * As well as acting like a normal controller, this controller also handles
 * requests on urls such as / and /index.php
 * through the indexAction method
 *
 * @category Namesco
 * @package  ZtalExample
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class IndexController extends Zend_Controller_Action
{
	/**
	 * The index action
	 *
	 * @return void
	 */
	public function indexAction()
	{
		// Nothing to do here - all in the template.
	}

	/**
	 * The form action.
	 *
	 * @return void
	 */
	public function formAction()
	{
		// Create the form
		$exampleForm = new Application_Form_Basic();

		// Check if there is some post data to work with
		if ($this->_request->isPost()) {

			// If there is post data, try to validate the form.
			if ($exampleForm->isValid($this->_request->getPost())) {

				// Input data is valid so grab all the values
				$submittedFormDetails = $exampleForm->getValues();
				var_dump($submittedFormDetails);
				exit();
			}
		}

		// If we get here either there was no post data or there was an error

		// Setup some defaults
		$defaults = array();
		$defaults['checkBox1'] = 1;
		$defaults['checkBox2'] = 0;
		$defaults['selectList'] = 'wibble';
		$defaults['textBox'] = 'Hello';
		$exampleForm->setDefaults($defaults);

		// Add the form object to the view
		$this->view->exampleForm = $exampleForm;

		// Set a title for the page
		$this->view->headTitle('Communication Option Details');
	}

	/**
	 * The table action.
	 *
	 * A basic table built from an array structure. Has sorting and pagination.
	 *
	 * @return void
	 */
	public function tableAction()
	{
		$tableData = array(
			array('col1' => 1, 'col2' => 'c'),
			array('col1' => 10, 'col2' => 'a'),
			array('col1' => 100, 'col2' => 'b'),
			array('col1' => 1000, 'col2' => 'd'),
			array('col1' => 10000, 'col2' => 'f'),
			array('col1' => 100000, 'col2' => 'e')
		);

		// Create a table instance
		$this->view->table = new Application_Table_Basic(
			$this->getRequest()->getUserParams());

		// set the data source for the table
		$this->view->table->setDataSource($tableData);
	}

	/**
	 * The object-table action
	 *
	 * @return void
	 */
	public function objectTableAction()
	{
		$tableData = array(
			new Application_Model_Basic(1, 1.2),
			new Application_Model_Basic(2, 1.7),
			new Application_Model_Basic(3, 1.1),
			new Application_Model_Basic(4, 1.5),
			new Application_Model_Basic(5, 2.3),
			new Application_Model_Basic(6, 0.6)
		);

		// Create a table instance
		$this->view->table = new Application_Table_Object(
			$this->getRequest()->getUserParams());

		// set the data source for the table
		$this->view->table->setDataSource($tableData);
	}

}

