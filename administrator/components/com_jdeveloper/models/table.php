<?php
/**
 * @package     JDeveloper
 * @subpackage  Models
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * JDeveloper Table Model
 *
 * @package     JDeveloper
 * @subpackage  Models
 */
class JDeveloperModelTable extends JModelAdmin
{
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		// Delete fields which belong to the table
		if ( (int) JComponentHelper::getParams('com_jdeveloper')->get('delete_fields') );
		{
			$model = JModelLegacy::getInstance('Field', 'JDeveloperModel');
			
			foreach ($pks as $pk)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$db->setQuery($query->select('f.id')->from('#__jdeveloper_fields AS f')->where('f.table = ' . $db->quote((int) $pk)));
				$ids = $db->loadRowList();
				$keys = array();
				foreach ($ids as $id) $keys[] = $id[0];
				$model->delete($keys);
				
				if (JFile::exists(JDeveloperARCHIVE . "/tables/table_$pk.xml"))
				 JFile::delete(JDeveloperARCHIVE . "/tables/table_$pk.xml");
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
	public function getTable($type = 'Table', $prefix = 'JDeveloperTable', $config = array())
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
		$form = $this->loadForm('tables', 'table', $options);
		
		if(empty($form))
		{
			return false;
		}
		return $form;
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
		$data = $app->getUserState('com_jdeveloper.edit.table.data', array());
		
		if (empty($data))
		{
			$data = $this->getItem();
		}
		
		return $data;
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
		JDeveloperLoader::import("create");

		if (false === $item = parent::getItem($pk))
		{
			return false;
		}
		
		$db = JFactory::getDbo();
		$params = JComponentHelper::getParams('com_jdeveloper');
		
		if (!empty($item->component)) {
			$component = JModelLegacy::getInstance('Component', 'JDeveloperModel')->getItem($item->component);
			
			// Only add component name to table name if it is not added already
			if (!preg_match("/^". $component->name ."/", $item->name)) {
				$item->dbname = $params->get('add_component_name_to_table_name') ? $component->name . '_' . $item->name : $item->name;
			}
			else {
				$item->dbname = $item->name;
			}
		}
		else {
			$item->dbname = $item->name;
		}
		
		$db->setQuery("SELECT f.name FROM #__jdeveloper_fields AS f WHERE f.table = " . $db->quote($item->id) . " ORDER BY f.ordering");
		$item->mainfield = $db->loadResult();

		$registry = new JRegistry();
		$registry->loadString($item->jfields);
		$item->jfields = $registry->toArray();
		
		return $item;
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
	 * Batch changes for a group of rows.
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

				if (property_exists($this->table, $field))
				{
					$this->table->set($field, $value);
				}
				else
				{
					$registry = new JRegistry();
					$registry->loadString($this->table->params);
					$registry->set($field, $value);
					$this->table->set('params', $registry->toString());
				}

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