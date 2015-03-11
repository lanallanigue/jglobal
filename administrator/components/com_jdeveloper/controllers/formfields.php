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
 * JDeveloper Formfields Controller
 *
 * @package     JDeveloper
 * @subpackage  Controllers
 */
class JDeveloperControllerFormfields extends JControllerAdmin
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Formfield', $prefix='JDeveloperModel', $config = array())
	{
		$config['ignore_request'] = true;
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Create form fields
	 */
	public function create()
	{		
		$app = JFactory::getApplication();
		$cid = $app->input->get("cid", array(), "array");
		$this->setRedirect(JRoute::_('index.php?option=com_jdeveloper&view=formfields', false));
		
		foreach ($cid as $id)
		{
			$item = $this->getModel()->getItem($id);
			echo print_r($item);
			JFile::write(JDeveloperARCHIVE . "/fields/" . strtolower($item->name) . ".php", $item->source);
		}
		
		$this->setMessage(JText::sprintf('COM_JDEVELOPER_FORMFIELDS_CREATED', count($cid)));
	}
}