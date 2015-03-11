<?php
/**
 * @package     JDeveloper
 * @subpackage  Create.Form
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JDeveloperLoader::import("form", JDeveloperCREATE);

/**
 * Form Create Class
 *
 * @package     JDeveloper
 * @subpackage  Create.Modue
 */
class JDeveloperCreateFormForm extends JDeveloperCreateForm
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "form.xml";

	/**
	 * @see	JDeveloperCreate
	 */
	protected function initialize()
	{
		$this->template->addPlaceholders(array(
			"fields" => $this->getFields()
		));

		return parent::initialize();
	}
	
	/**
	 * Create fields
	 *
	 * @return	string
	 */
	private function getFields()
	{
		$model = $this->getModel("Form");
		$table = $model->getTable();
		$buffer = "";
		
		//Form has no fields
		if ($table->isLeaf($this->item->id))
		{
			return "";
		}
		
		$children = $table->getTree($this->item->id);

		foreach ($children as $field)
		{
			if ($field->level != $this->item->level + 1)
			{
				continue;
			}
			elseif ($field->id == $this->item->id)
			{
				continue;
			}
			elseif ($table->isLeaf($field->id))
			{
				$buffer .= JDeveloperCreate::getInstance("form.field", array("item_id" => $field->id))->getBuffer();
			}
			else
			{
				$buffer .= JDeveloperCreate::getInstance("form.fieldarray", array("item_id" => $field->id))->getBuffer();
			}
		}
		
		return $buffer;
	}
}