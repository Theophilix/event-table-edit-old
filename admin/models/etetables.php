<?php
/**
 * $Id: $.
 *
 * @copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license GNU/GPL, see LICENSE.php in the installation package
 * This file is part of Event Table Edit
 *
 * Event Table Edit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Event Table Edit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Event Table Edit. If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

class EventtableeditModelEtetables extends JModelList
{
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return void
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication();
        $input = $app->input;
        // Adjust the context to support modal layouts.
        if ($layout = $input->get('view')) {
            $this->context .= '.'.$layout;
        }

        $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
        $this->setState('filter.access', $access);

        $published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        $ordering = $app->getUserStateFromRequest($this->context.'list.ordering', 'filter_order', '');
        $this->setState('list.ordering', $ordering);

        $direction = $app->getUserStateFromRequest($this->context.'list.direction', 'filter_order_Dir', '');
        $this->setState('list.direction', $direction);
        if (!$ordering) {
            $ordering = 'a.id';
        }
        if (!$direction) {
            $direction = 'asc';
        }
        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string $id a prefix for the store id
     *
     * @return string a store id
     *
     * @since	1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':'.$this->getState('filter.search');
        $id .= ':'.$this->getState('filter.access');
        $id .= ':'.$this->getState('filter.published');
        $id .= ':'.$this->getState('filter.language');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return JDatabaseQuery
     *
     * @since	1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $input = JFactory::getApplication()->input;
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.*'
            )
        );
        $query->from('#__eventtableedit_details AS a');

        // Join over the language
        $query->select('l.title AS language_title');
        $query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = '.(int) $access);
        }

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = '.(int) $published);
        } elseif ('' === $published) {
            $query->where('(a.published = 0 OR a.published = 1)');
        }

        // Filter by search in name.
        /*	$search = $this->getState('filter.search');
            if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                    $query->where('a.id = '.(int) substr($search, 3));
                }
                else {
                    $search = $db->Quote('%'.$db->escape($search, true).'%');
                    $query->where('(a.name LIKE '.$search.' OR a.alias LIKE '.$search.')');
                }
            }*/
        $search = $input->get('filter_search');
        if ('' !== $search) {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
            $query->where('(a.name LIKE '.$search.')');
        }
        // Filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where('a.language = '.$db->quote($language));
        }

        // condition to check table is normal or apppointment
        $query->where('a.normalorappointment = 0');

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', '');
        $orderDirn = $this->state->get('list.direction', '');

        //$query->order($db->escape($orderCol.' '.$orderDirn));
        $query = $query.' ORDER BY '.$orderCol.' '.$orderDirn;
        //exit;
        return $query;
    }
}
