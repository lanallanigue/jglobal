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
 * JDeveloper Form Controller
 *
 * @package     JDeveloper
 * @subpackage  Controllers
 */
class JDeveloperControllerForm extends JControllerForm
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $view_item = 'Form';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $view_list = 'Forms';
	
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
		$model = $this->getModel('Form', 'JDeveloperModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jdeveloper&view=forms' . $this->getRedirectToListAppend(), false));

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
		
		$parent_id = $this->input->get('parent_id', 0, 'int');
		if ($parent_id)
		{
			$append .= "&parent_id=" . $parent_id;
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

		$parent_id = $this->input->get('parent_id', 0, 'int');
		if ($parent_id)
		{
			$append .= "&parent_id=" . $parent_id;
		}
		
		return $append;
	}
}
?>