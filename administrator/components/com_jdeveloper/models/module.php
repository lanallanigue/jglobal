<?php
/**
 * @package     JDeveloper
 * @subpackage  Models
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JDeveloperLoader::import("install");

/**
 * JDeveloper Module Model
 *
 * @package     JDeveloper
 * @subpackage  Models
 */
class JDeveloperModelModule extends JModelAdmin
{
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete(&$pks)
	{
		$model_pl = JModelLegacy::getInstance("Overrides", "JDeveloperModel");
		$model_sg = JModelLegacy::getInstance("Override", "JDeveloperModel");

		// Delete overrides
		foreach ($pks as $pk)
		{
			$overrides = $model_pl->getOverrides("module", $pk);
			if (count($overrides))
			{
				foreach ($overrides as $override)
				{
					$model_sg->delete($override->id);
				}
			}
		}
		
		return parent::delete($pks);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($type = 'Module', $prefix = 'JDeveloperTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   12.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$options = array('control' => 'jform', 'load_data' =>$loadData);
		$form = $this->loadForm('module', 'module', $options);
		
		if(empty($form))
		{
			return false;
		}
		
		return $form;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItem($pk = null)
	{
		$app = JFactory::getApplication();
		$itemId = $this->getState("data.id", 0);
		$layout = $app->input->get("layout", "default", "string");

		if (empty($pk) && $layout == "default" && !empty($itemId))
		{
			$pk = $itemId;
		}

		if (false === $item = parent::getItem($pk))
		{
			return false;
		}
		
		$item->installed = JDeveloperInstall::isInstalled("module", "mod_" . $item->name);
		$item->createDir = JDeveloperArchive::getArchiveDir() . "/" . JDeveloperArchive::getArchiveName("mod_", $item->name, $item->version);

		$params = JComponentHelper::getParams("com_jdeveloper");
			
		if (empty($item->params['author']))			$item->params['author'] = $params->get("author", "");
		if (empty($item->params['author_email']))	$item->params['author_email'] = $params->get("author_email", "");
		if (empty($item->params['author_url']))		$item->params['author_url'] = $params->get("author_url", "");
		if (empty($item->params['copyright']))		$item->params['copyright'] = $params->get("copyright", "");
		if (empty($item->params['license']))		$item->params['license'] = $params->get("license", "");

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   12.2
	 */
	protected function loadFormData()
	{
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_jdeveloper.edit.module.data', array());
		
		if(empty($data)) {
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		
		$inputId = $app->input->get("id", 0, "int");
		$sessionId = $session->get($this->getName() . ".id", 0, $this->getName() . ".data");
		$layout = $app->input->get("layout", "default", "string");

		if ($layout == "default" && !empty($inputId))
		{
			$session->set($this->getName() . '.id', $inputId, $this->getName() . ".data");
			$this->setState("data.id", $inputId);
		}
		elseif ($layout == "default" && !empty($sessionId))
		{
			$this->setState("data.id", $sessionId);			
		}
		
		parent::populateState();
	}
	
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($commands, $pks, $contexts)
	{		
		// Set some needed variables.
		$this->user = JFactory::getUser();
		$this->table = $this->getTable();
		$this->tableClassName = get_class($this->table);
		$this->contentType = new JUcmType;
		$this->type = $this->contentType->getTypeByTable($this->tableClassName);
		$this->batchSet = true;

		foreach ($commands as $field => $value)
		{
			if ($value != "")
			{
				if (!$this->batchCustom($field, $value, $pks, $contexts))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Batch site changes for a group of rows.
	 *
	 * @param   string  $field     The field.
	 * @param   string  $value     The new value for field site.
	 * @param   array   $pks       An array of row IDs.
	 * @param   array   $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   11.3
	 */
	protected function batchCustom($field, $value, $pks, $contexts)
	{
		if (!$this->batchSet)
		{
			// Set some needed variables.
			$this->user = JFactory::getUser();
			$this->table = $this->getTable();
			$this->tableClassName = get_class($this->table);
			$this->contentType = new JUcmType;
			$this->type = $this->contentType->getTypeByTable($this->tableClassName);
			$this->batchSet = true;
		}

		foreach ($pks as $pk)
		{
			if ($this->user->authorise('core.edit', 'com_jdeveloper'))
			{				
				$this->table->reset();
				$this->table->load($pk);
				$this->table->set($field, $value);

				static::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

				if (!$this->table->store())
				{
					$this->setError($this->table->getError());
					return false;
				}
			}
			else
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
}