<?php
/**
 * @package     JDeveloper
 * @subpackage  Create.Module
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
JDeveloperLoader::import("module", JDeveloperCREATE);

/**
 * Module Create Class
 *
 * @package     JDeveloper
 * @subpackage  Create.Modue
 */
class JDeveloperCreateModuleManifest extends JDeveloperCreateModule
{		
	/**
	 * The template file
	 *
	 * @var	string
	 */
	protected $templateFile = "module.xml";
	
	public function initialize()
	{
		$this->template->addPlaceHolders(
			array( 
			'languages' => $this->lang()
			)
		);
		
		return parent::initialize();
	}

	private function lang()
	{
		$buffer = '';
		$name = $this->item->name;
		$languages = $this->item->params["languages"];

		foreach ($languages as $lang)
		{
			$buffer .= "\n\t\t<language tag=\"$lang\">language/$lang.mod_$name.ini</language>";
			$buffer .= "\n\t\t<language tag=\"$lang\">language/$lang.mod_$name.sys.ini</language>";
		}

		return $buffer;
	}

}