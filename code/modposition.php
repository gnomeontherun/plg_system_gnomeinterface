<?php

/**
 * @version     $Id$
 * @category	Joomla
 * @package     Plugins
 * @subpackage  Modpoisition
 * @copyright   Copyright (C) 2011 Gnome on the run. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/gpl.html>
 * @author      Jeremy Wilken - Gnome on the run
 * @link        http://www.gnomeontherun.com
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemModposition extends JPlugin
{

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onAfterRoute()
	{
		$app = JFactory::getApplication();
		if ($app->isAdmin()) 
		{
		
			if (JRequest::getCmd('option') == 'com_modules' && JRequest::getCmd('view') == 'select')
			{
				$doc = JFactory::getDocument();
				$doc->addStyleDeclaration("
				ul#new-modules-list, ul#new-modules-list2 {
					border-top: 1px solid #666666;
					list-style: none outside none;
					margin: 0;
					float: left;
					padding: 5px 0 0 15px;
					width: 45%;
				}
				ul#new-modules-list li, ul#new-modules-list2 li {
					display: block;
					float: none;
					list-style: none outside none;
					margin: 0 20px 0 0;
					width: 100%;
				}
				ul#new-modules-list li a, ul#new-modules-list2 li a {
					font-size: 1.091em;
					line-height: 1.8em;
				}
				ul#new-modules-list2 {
					float: right;
				}
				");
				$doc->addScriptDeclaration("
				window.addEvent('domready', function() {
					var index = 1;
					var right = new Element('ul', {'id':'new-modules-list2'});
					var midpoint = Math.round($$('#new-modules-list li').length / 2);
					$$('#new-modules-list li').each(function(element) { 
						if (index > midpoint) {
							element.inject(right);
						} else {
							element.setStyle('float', 'none');
						}
						index++;
					});
					right.inject('new-modules-list', 'after'); 
				});
				");
			}
		
			if (JRequest::getCmd('option') == 'com_modules' && JRequest::getCmd('view') == 'module' && JRequest::getCmd('layout') == 'edit')
		 	{
				$filters = '';
				if ($this->params->get('filter_status')) $filters .= '&filter_status='.$this->params->get('filter_status');
				if ($this->params->get('filter_type')) $filters .= '&filter_type='.$this->params->get('filter_type');
				if ($this->params->get('filter_template')) $filters .= '&filter_template='.$this->params->get('filter_template');
				
				$db = JFactory::getDBO();
				$db->setQuery("SELECT DISTINCT position FROM #__modules WHERE client_id = 0 AND published != '-2'");
				$positions = $db->loadResultArray();
				
				$db->setQuery("SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1");
				$template = $db->loadResult();
				
				$xml = simplexml_load_file(JPATH_ROOT.DS.'templates'.DS.$template.DS.'templateDetails.xml');
				$positions = array_merge($positions, $xml->xpath('//position'));
				$positions = array_unique($positions);
				sort($positions);
				
				$doc = JFactory::getDocument();
				$script = "
				var position_default;
				window.addEvent('domready', function() {
					position_default = $('jform_position').get('value');
					var button = $('jform_position-lbl').getNext('div.button2-left').getFirst('div').getFirst('a.modal');
					var link = button.get('href');
					button.set('href', link+'".$filters."');
					var list = new Element('select', {'id':'position_list'});
					list.adopt(new Element('option', {'value':'','text':'".JText::_('PLG_SYSTEM_MODPOSITION_SELECT')."'}));
					";
					
				foreach ($positions as $position) {
					$script .= "list.adopt(new Element('option', {'value':'".$position."','text':'".$position."'}));".PHP_EOL;
				}
					
				$script .= "
					list.injectAfter('jform_position');
					$('position_list').addEvent('change', function() {
						if (this.get('value') != '') {
							$('jform_position').set('value', this.get('value'));
						} else {
							$('jform_position').set('value', position_default);
						}
					});
				});
				";
				
				$doc->addScriptDeclaration($script);
			}
		}
	}

} 