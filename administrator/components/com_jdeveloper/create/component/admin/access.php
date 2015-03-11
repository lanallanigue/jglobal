<?php
/**
 * @package     JDeveloper
 * @subpackage  Create.Component
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JDeveloperLoader::import("component", JDeveloperCREATE);

/**
 * Component Create Class
 *
 * @package     JDeveloper
 * @subpackage  Create.Component
 */
class JDeveloperCreateComponentAdminAccess extends JDeveloperCreateComponent
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "admin.access.xml";

	protected function initialize()
	{
		$this->template->addPlaceHolders(
			array( 
			'Sections' => $this->sections(),
			)
		);
		
		return parent::initialize();
	}
	
	private function sections()
	{
		$buffer = '';
		$template = $this->loadSubtemplate('section.xml');
		
		foreach ($this->tables as $table)
		{
			if ((bool) $table->jfields['asset_id'])
			{
				$template->addPlaceholders(array('singular' => strtolower($table->singular)));
				$template->addAreas(array('published' => (bool) $table->jfields['published']));
				$buffer .= $template->getBuffer();
			}
		}

		return $buffer;
	}
}