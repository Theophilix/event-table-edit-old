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

class EventtableeditModelDropdowns extends JModelList
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

        $published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

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

        // Select the required fields from the table.
        $query->select(
            $this->getState('list.select', 'a.*')
        );
        $query->from('#__eventtableedit_dropdowns AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = '.(int) $published);
        } elseif ('' === $published) {
            $query->where('(a.published = 0 OR a.published = 1)');
        }

        // Filter by search in name.
        //$search = $this->getState('filter.search');
        $input = JFactory::getApplication()->input;
        $search = $input->get('filter_search');
        /*if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            }
            else {
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('(a.name LIKE '.$search.')');
            }
        }*/
        if ('' !== $search) {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
            $query->where('(a.name LIKE '.$search.')');
        }
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        //$query->order($db->escape($orderCol.' '.$orderDirn));
        $query = $query.' ORDER BY '.$orderCol.' '.$orderDirn;
        return $query;
    }
}
