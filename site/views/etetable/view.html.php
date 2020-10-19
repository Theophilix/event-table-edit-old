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
require_once JPATH_COMPONENT.'/models/etetable.php';

class EventtableeditViewEtetable extends JViewLegacy
{
    protected $state;
    protected $item;
    protected $heads;
    protected $dropdowns;
    protected $rows;
    protected $pagination;
    protected $print;

    public function display($tpl = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->unique = 'ETE_'.$this->item->alias.'_'.rand(0, 999);

        // Check for errors.
        if (!$this->checkError()) {
            return false;
        }

        $this->heads = $this->get('Heads');
        $this->dropdowns = $this->get('Dropdowns');
        $this->rows = $this->get('Rows');
        $this->pagination = $this->get('Pagination');
        $main = $app->input;
        $this->print = $main->get('print');
        $filterstring = $main->get('filterstring');

        // Check for errors.
        if (!$this->checkError()) {
            return false;
        }

        // Get the parameters of the active menu item
        $params = $app->getParams();
        $params->merge($this->item->params);
        $params->set('filterstring', $filterstring);

        // check if access is not public
        $groups = $user->getAuthorisedViewLevels();

        $rows = $this->rows['rows'];
        $additional = $this->rows['additional'];
        $additional['defaultSorting'] = $this->isDefaultSorted();
        $additional['dropdowns'] = $this->buildDropdownJsArray();
        $additional['containsDate'] = $this->containsDate();

        if (isset($active->query['layout'])) {
            // We need to set the layout in case this is an alternative menu item (with an alternative layout)
            $this->setLayout($active->query['layout']);
        }

        // Added language variables.
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_LAYOUTMODE');
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_STACK');
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_SWIPE');
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_TOGGLE');

        $this->assignRef('params', $params);
        $this->assignRef('item', $this->item);
        $this->assignRef('heads', $this->heads);
        $this->assignRef('rows', $rows);
        $this->assignRef('additional', $additional);
        $this->assignRef('state', $this->state);
        $this->assignRef('pagination', $this->pagination);
        $this->assignRef('print', $this->print);

        $this->_prepareDocument();

        if (!$this->checkAccess()) {
            return false;
        }

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
    protected function checkAccess()
    {
        $user = JFactory::getUser();

        $userAccess = $user->getAuthorisedViewLevels();

        if (in_array($this->item->access, $userAccess)) {
            return true;
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            return false;
        }
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

        // Handle Printview
        if ($this->print) {
            $this->preparePrintView();
        } else {
            /* require_once JPATH_COMPONENT.'/helpers/phpToJs.php';
            $doc = JFactory::getDocument();
            $this->document->addScriptDeclaration('
                                                    var newest = "'.JText::_('COM_EVENTTABLEEDIT_NEWEST').'";
                                                    var oldest = "'.JText::_('COM_EVENTTABLEEDIT_OLDEST').'";

                                                    ');
            $this->document->addScript($this->baseurl.'/components/com_eventtableedit/template/js/tablesaw.js?v3');
            $this->document->addScript($this->baseurl.'/components/com_eventtableedit/template/js/tablesaw-init.js');


            $this->document->addScript($this->baseurl.'/components/com_eventtableedit/helpers/tableAjax.js');
            $this->document->addScript($this->baseurl.'/components/com_eventtableedit/helpers/popup.js?v4'); */
            //	$this->document->addScript($this->baseurl.'/components/com_eventtableedit/template/js/jquery.js');

            $script = 'function initClickEvent(){';
            $script .= 'initClickEvent_'.$this->unique.'();';
            $script .= '}';
            $this->document->addScriptDeclaration($script);

            require JPATH_SITE.'/plugins/content/loadete/phpToJs.php';
            $doc = JFactory::getDocument();
            $this->document->addScriptDeclaration('
													var newest = "'.JText::_('COM_EVENTTABLEEDIT_NEWEST').'";
													var oldest = "'.JText::_('COM_EVENTTABLEEDIT_OLDEST').'";
													var unique = "'.$this->unique.'";
													
													');
            $this->document->addScript($this->baseurl.'/components/com_eventtableedit/template/js/tablesaw.js?v3');
            $this->document->addScript($this->baseurl.'/components/com_eventtableedit/template/js/tablesaw-init.js');

            require JPATH_SITE.'/plugins/content/loadete/tableAjax.php';
            require_once JPATH_SITE.'/plugins/content/loadete/popup.php';
        }
    }

    /**
     * See if any column is defaultSorted.
     */
    private function isDefaultSorted()
    {
        if (!count($this->heads)) {
            return 0;
        }

        foreach ($this->heads as $head) {
            if ('' !== $head->defaultSorting && ':' !== $head->defaultSorting) {
                return 1;
            }
        }
        return 0;
    }

    /**
     * Create a String that can be parsed easily into a javascript array.
     */
    private function buildDropdownJsArray()
    {
        $ret = [];

        for ($a = 0; $a < count($this->dropdowns); ++$a) {
            // If Dropdown was deleted
            if (null === $this->dropdowns[$a]['name']) {
                $ret[$a]['meta']['name'] = '';
                $ret[$a]['meta']['id'] = -1;
                continue;
            }

            $ret[$a]['meta']['name'] = $this->dropdowns[$a]['name']['name'];
            $ret[$a]['meta']['id'] = $this->dropdowns[$a]['name']['id'];

            if (!count($this->dropdowns[$a]['items'])) {
                continue;
            }

            foreach ($this->dropdowns[$a]['items'] as $item) {
                $ret[$a]['items'][] = $item->name;
            }
        }

        return $ret;
    }

    /**
     * Change the settings for printview.
     */
    private function preparePrintView()
    {
        $this->document->setMetaData('robots', 'noindex, nofollow');
        $this->params->set('access-add', 0);
        $this->params->set('access-create_admin', 0);
        $this->item->show_filter = 0;
    }

    private function getVariableStyles($cellspacing, $cellpadding, $linecolor0, $linecolor1)
    {
		
        $style = [];
        $style[] = '#etetable-table td {padding: '.$cellpadding.'px;}';
        $style[] = '@media screen and (min-width: 959px) and  (max-width: 995px) { #etetable-table td {padding: 0px;} }';
        //	$style[] = ".etetable-linecolor0 {background-color: #" . $linecolor0 . ";}";
        //	$style[] = ".etetable-linecolor1 {background-color: #" . $linecolor1 . ";}";
        $style[] = '.tablesaw tbody tr:nth-child(odd) {background-color: #'.(($linecolor0)?$linecolor0:'#ffffff').';}';
        $style[] = '.tablesaw tbody tr:nth-child(even) {background-color: #'.(($linecolor1)?$linecolor1:'#ffffff').';}';

        if (0 !== (int) $cellspacing) {
            $style[] = '#etetable-table {border-collapse: separate !important;}';
        }

        // If Pagination must not be shown
        /* if (!$this->item->show_pagination) {
            $style[] = ".eventtableedit .limit {display: none;}";
        } */
        $style[] = '.eventtableedit .limit {display: none;}';

        if (0 === (int)$this->item->rowsort) {
            $style[] = '.eventtableedit .sort_col {display: none !important;}';
            //$style[] = ".eventtableedit .tablesaw-priority-50 {display: none !important;}";
        }

        if (!$this->params->get('access-reorder')) {
            $style[] = '.eventtableedit .sort_col {display: none !important;}';
        }

        if (!$this->params->get('access-delete')) {
            $style[] = '.eventtableedit .del_col {display: none !important;}';
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

    /**
     * Show a date picker, if at least one column is a date.
     */
    private function containsDate()
    {
        if (!count($this->heads)) {
            return false;
        }

        foreach ($this->heads as $row) {
            if ('date' === $row->datatype) {
                return true;
            }
        }
        return false;
    }
}
