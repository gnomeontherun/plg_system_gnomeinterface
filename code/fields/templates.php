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

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldTemplates extends JFormFieldList
{

	public $type = 'Templates';

	protected function getOptions()
	{
		
		// Get the database object and a new query object.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('tbl.name, tbl.element');
		$query->from('#__extensions as tbl');
		$query->where('tbl.type = "template"');
		$query->order('tbl.name');

		// Set the query and load the styles.
		$db->setQuery($query);
		$styles = $db->loadObjectList();
		
		// Initialize variables.
		$options = array();
		
		$options[] = JHtml::_('select.option', '', JText::_('JOPTION_SELECT_PUBLISHED'), 'value', 'text', false);

		foreach ($styles as $option) {

			// Create a new option object based on the element.
			$tmp = JHtml::_('select.option', (string) $option->element, JText::alt(trim((string) $option->name), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', false);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
