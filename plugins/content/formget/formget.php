<?php
/**
 * @package		 Rimotevst Plugins
 * @subpackage	 FormGet
 * @author       Neeraj Agarwal
 * @copyright    Copyright (C) 2013 Neeraj <neeraj@formget.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * FormGet is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
class plgContentFormget extends JPlugin {
	public function __construct(& $subject, $config)
    {
            parent::__construct($subject, $config);
            $this->loadLanguage();
			$app = JFactory::getApplication();
			if ( $app->getClientId() === 0 ) {
						echo $this->params->get('tabview', NULL);
			}else{			echo '<style>.width-40{width:100% !important;}</style>';			}
	}
}