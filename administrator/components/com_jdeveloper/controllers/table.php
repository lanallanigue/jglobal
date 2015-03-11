<?php
/**
 * @package     JDeveloper
 * @subpackage  Controllers
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JDeveloper Table Controller
 *
 * @package     JDeveloper
 * @subpackage  Controllers
 */
class JDeveloperControllerTable extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   12.2
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if ($this->input->get("component", 0, "int"))
		{
			$this->view_list = "component";
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Table', 'JDeveloperModel');

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jdeveloper&view=tables', false));

		return parent::batch($model);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId = null, $urlVar = 'id');
		
		$id = $this->input->get('id', 0, 'int');
		if ($id)
		{
			$append .= "&id=" . $id;
		}
		
		$component = $this->input->get('component', 0, 'int');
		if ($component)
		{
			$append .= "&component=" . $component;
		}
		
		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();

		$component = $this->input->get('component', 0, 'int');
		if ($component)
		{
			$append .= "&id=" . $component;
		}
		
		return $append;
	}		
}