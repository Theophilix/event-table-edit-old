<?php
/**
 * $Id: view.html.php 140 2011-01-11 08:11:30Z kapsl $.
 *
 * @copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

class EventtableeditViewEtetable extends JViewLegacy
{
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null)
    {
        // Initialiase variables.
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        $this->fields = $this->get('Fields');
        $jinput = JFactory::getApplication()->input;
        $this->id = $jinput->get('id');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            foreach ($errors as $error) {
                JFactory::getApplication()->enqueueMessage($error, 'error');
            }
            return false;
        }

        $this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $userId = $user->get('id');
        $isNew = (0 === (int)$this->item->id);
        $checkedOut = !(0 === (int)$this->item->checked_out || $this->item->checked_out === $userId);
        $canDo = eteHelper::getActions($this->state->get('filter'));

        //JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_MANAGER_ETETABLE'), 'etetable');
        $xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/eventtableedit.xml');
        $currentversion = (string) $xml->version;
        JToolBarHelper::title(JText::_('Event Table Edit '.$currentversion).' - '.JText::_('COM_EVENTTABLEEDIT_MANAGER_ETETABLE'), 'etetables');
        // Built the actions for new and existing records.
        if ($isNew) {
            // For new records, check the create permission.
            if ($canDo->get('core.create')) {
                JToolBarHelper::apply('etetable.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('etetable.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom('etetable.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }

            JToolBarHelper::cancel('etetable.cancel', 'JTOOLBAR_CANCEL');
        } else {
            // Can't save the record if it's checked out.
            if (!$checkedOut) {
                // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
                if ($canDo->get('core.edit')) {
                    JToolBarHelper::apply('etetable.apply', 'JTOOLBAR_APPLY');
                    JToolBarHelper::save('etetable.save', 'JTOOLBAR_SAVE');

                    // We can save this record, but check the create permission to see if we can return to make a new one.
                    if ($canDo->get('core.create')) {
                        JToolBarHelper::custom('etetable.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                    }
                }
            }

            // If checked out, we can still save
            if ($canDo->get('core.create')) {
                JToolBarHelper::custom('etetable.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }

            JToolBarHelper::cancel('etetable.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
