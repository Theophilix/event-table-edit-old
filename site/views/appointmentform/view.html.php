<?php

/**
 * $Id: view.html.php 157 2011-03-19 00:08:23Z kapsl $.
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

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT.'/models/appointmentform.php';

class EventtableeditViewappointmentform extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $heads;

    protected $rows;

    public function display($tpl = null)
    {
        // Initialise variables.

        $app = JFactory::getApplication();

        $user = JFactory::getUser();

        $this->state = $this->get('State');

        $this->item = $this->get('Item');

        // Check for errors.

        if (!$this->checkError()) {
            return false;
        }

        $this->heads = $this->get('Heads');

        $this->rows = $this->get('Rows');

        $main = $app->input;

        // Check for errors.

        if (!$this->checkError()) {
            return false;
        }

        // Get the parameters of the active menu item

        $params = $app->getParams();

        $params->merge($this->item->params);

        // check if access is not public

        $groups = $user->getAuthorisedViewLevels();

        $rows = $this->rows['rows'];

        if (isset($active->query['layout'])) {
            // We need to set the layout in case this is an alternative menu item (with an alternative layout)

            $this->setLayout($active->query['layout']);
        }

        $this->assignRef('params', $params);

        $this->assignRef('item', $this->item);

        $this->assignRef('heads', $this->heads);

        $this->assignRef('rows', $rows);

        $this->_prepareDocument();

        parent::display($tpl);
    }

    private function checkError()
    {
        if (count($errors = $this->get('Errors'))) {
            foreach ($errors as $error) {
                JFactory::getApplication()->enqueueMessage($error, 'warning');
            }
            return false;
        }

        return true;
    }

    /**
     * Prepares the document.
     */
    protected function _prepareDocument()
    {
        $app = JFactory::getApplication();

        $menus = $app->getMenu();

        $pathway = $app->getPathway();

        $title = null;

        JHTML::_('behavior.tooltip');

        JHTML::_('behavior.calendar');

        JHtml::_('behavior.framework');

        // Because the application sets a default page title,

        // we need to get it from the menu item itself

        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_EVENTTABLEEDIT_DEFAULT_PAGE_TITLE'));
        }

        $id = (int) @$menu->query['id'];

        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = htmlspecialchars_decode($app->getCfg('sitename'));
        } elseif ($app->getCfg('sitename_pagetitles', 0)) {
            $title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
        }

        // Add css

        $this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/tablesaw.css');

        $this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtablecss.css');

        $this->document->addStyleDeclaration($this->getVariableStyles($this->item->cellspacing, $this->item->cellpadding, $this->item->tablecolor1, $this->item->tablecolor2));

        $this->document->addCustomTag($this->getBrowserStyles());

        $this->document->setTitle($title);

        if (empty($title)) {
            $title = $this->item->title;

            $this->document->setTitle($title);
        }

        if ($this->item->metadesc) {
            $this->document->setDescription($this->item->metadesc);
        }

        if ($this->item->metakey) {
            $this->document->setMetadata('keywords', $this->item->metakey);
        }

        if ('1' === $app->getCfg('MetaTitle')) {
            $this->document->setMetaData('title', $this->item->name);
        }

        $mdata = $this->item->metadata->toArray();

        foreach ($mdata as $k => $v) {
            if ($v) {
                $this->document->setMetadata($k, $v);
            }
        }
    }

    private function getVariableStyles($cellspacing, $cellpadding, $linecolor0, $linecolor1)
    {
        $style = [];

        $style[] = '#etetable-table td {padding: '.$cellpadding.'px;}';

        //$style[] = '.etetable-linecolor0 {background-color: #'.($linecolor0)?$linecolor0:'cccccc'.';}';
        //$style[] = '.etetable-linecolor1 {background-color: #'.($linecolor1)?$linecolor1:'#ffffff'.';}';

        if (0 !== (int) $cellspacing) {
            $style[] = '#etetable-table {border-collapse: separate !important;}';
        }

        return implode("\n", $style);
    }

    /**
     * Especially for IE that the calendar is on the right position.
     */
    private function getBrowserStyles()
    {
        $ie = '<!--[if IE]>'."\n";

        $ie .= '<link rel="stylesheet" href="'.$this->baseurl.'/components/com_eventtableedit/template/css/ie.css" />'."\n";

        $ie .= '<![endif]-->'."\n";

        $ie .= '<!--[if lte IE 7]>'."\n";

        $ie .= '<link rel="stylesheet" href="'.$this->baseurl.'/components/com_eventtableedit/template/css/ie7.css" />'."\n";

        $ie .= '<![endif]-->'."\n";

        return $ie;
    }
}

?>

