<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class EventtableeditControllerEtetable extends JControllerForm
{
    protected $text_prefix = 'COM_EVENTTABLEEDIT_ETETABLE';

    /**
     * Method override to check if you can add a new record.
     *
     * @param array $data an array of input data
     *
     * @return bool
     *
     * @since	1.6
     */
    protected function allowAdd($data = [])
    {
        // Initialise variables.
        $user = JFactory::getUser();
        $allow = null;

        if (null === $allow) {
            // In the absense of better information, revert to the component permissions.
            return parent::allowAdd($data);
        } else {
            return $allow;
        }
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param array  $data an array of input data
     * @param string $key  the name of the key for the primary key
     *
     * @return bool
     *
     * @since	1.6
     */
    protected function allowEdit($data = [], $key = 'id')
    {
        // Initialise variables.
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = JFactory::getUser();
        $userId = $user->get('id');

        // Check general edit permission first.
        if ($user->authorise('core.edit', $this->option)) {
            return true;
        }

        // Since there is no asset tracking, revert to the component permissions.
        return parent::allowEdit($data, $key);
    }
}
