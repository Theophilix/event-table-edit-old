<?php
/**
 * $Id: controller.php 140 2011-01-11 08:11:30Z kapsl $
 * @copyright (C) 2007 - 2018 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

class EventtableeditController extends JControllerLegacy {
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'etetables';
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false) {
		require_once JPATH_COMPONENT.'/helpers/ete.php';
		
		// Override standard view

		$input  =  JFactory::getApplication()->input;
		$view	= $input->get('view','etetables');
		if ($view == 'eventtableedit') {
			$view = 'etetables';
		}
		
		// Load the submenu.
		eteHelper::addSubmenu($view);
		
		$layout = $input->get('layout');
		$id		= $input->get('id');
		
		// Check for edit form.
		if ($view == 'etetable' && $layout == 'edit' && !$this->checkEditId('com_eventtableedit.edit.etetable', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit&view=etetables', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
?>
