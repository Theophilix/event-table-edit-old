<?php
/**
 * $Id: controller.php 140 2011-01-11 08:11:30Z kapsl $.
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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class EventtableeditController extends JControllerLegacy
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;

        // Get the document object.
        $document = JFactory::getDocument();

        // Set the default view name and format from the Request.
        $main = JFactory::getApplication()->input;
        $vName = $main->get('view', '');
        $main->get('view', $vName);

        $user = JFactory::getUser();

        $safeurlparams = ['id' => 'INT', 'cid' => 'ARRAY', 'limit' => 'INT', 'limitstart' => 'INT',
            'filter' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'filterstring' => 'STRING',
            'filter_order' => 'STRING', 'filter_order_Dir' => 'STRING', ];

        parent::display($cachable, $safeurlparams);

        return $this;
    }
}
