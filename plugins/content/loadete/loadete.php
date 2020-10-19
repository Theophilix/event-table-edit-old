<?php
/**
 * $Id: loadete.php 140 2011-01-11 08:11:30Z kapsl $.
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
defined('_JEXEC') or die;
//require_once JPATH_SITE.'/components/com_eventtableedit/helpers/etetable.php';

class PlgContentLoadete extends JPlugin
{
    protected static $modules = [];

    protected static $mods = [];

    protected $uniques = [];

    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if ('com_finder.indexer' === $context) {
            return true;
        }

        if (false === strpos($article->text, 'ETE')) {
            return true;
        }

        $regex = '/{ETE\s(.*?)}/i';

        preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $match) {
                if ($match[1]) {
                    $tableName = $match[1];

                    $output = $this->_load($tableName);

                    $article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
                    $style = $this->params->def('style', 'none');
                }
            }

            $script = 'function initClickEvent(){';

            foreach ($this->uniques as $uniques) {
                $script .= 'initClickEvent_'.$uniques.'();';
            }

            $script .= '}';
            $document = JFactory::getDocument();
            $document->addScriptDeclaration($script);
        }
    }

    public function onAfterRenderModule(&$module, &$attribs)
    {
        if ('mod_custom' !== $module->module) {
            return;
        }

        if (false === strpos($module->content, 'ETE')) {
            return true;
        }

        $regex = '/{ETE\s(.*?)}/i';

        preg_match_all($regex, $module->content, $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $match) {
                if ($match[1]) {
                    $tableName = $match[1];

                    $output = $this->_load($tableName);

                    $module->content = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $module->content, 1);
                    $style = $this->params->def('style', 'none');
                }
            }

            $script = 'function initClickEvent(){';

            foreach ($this->uniques as $uniques) {
                $script .= 'initClickEvent_'.$uniques.'();';
            }

            $script .= '}';
            $document = JFactory::getDocument();
            $document->addScriptDeclaration($script);
        }
    }

    private function getTableID($tableName)
    {
        $db = JFactory::getDbo();
        $db->setQuery("SELECT * FROM #__eventtableedit_details where alias = '$tableName'");
        $result = $db->loadObject();
        return isset($result->id) ? $result->id : 0;
    }

    protected function _load($tableName = '')
    {
        if ('' === $tableName) {
            return;
        }

        $id = $this->getTableID($tableName);
        if (0 === $id) {
            return;
        }

        if (JFactory::getApplication()->isSite()) {
            require_once JPATH_SITE.''.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'etetable.php';
        }
        $language = JFactory::getLanguage();
        $boolan = $language->load('com_eventtableedit', JPATH_SITE);
        //load model
        JModelLegacy::addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'models');

        //get instance of model class, where class name will be EventtableeditModel
        $model = JModelLegacy::getInstance('Etetable', 'EventtableeditModel');

        // Initialise variables.
        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        $this->state = $model->getState();
        $model->setState('etetable.id', $id);
        $this->item = $model->getItem();
        $this->unique = 'ETE_'.$this->item->alias.'_'.rand(0, 999);
        $this->uniques[] = $this->unique;

        // Check for errors.
        if (!$this->checkError()) {
            return false;
        }

        $this->heads = $model->getHeads();
        $this->dropdowns = $model->getDropdowns();

        $this->rows = $model->getRows();

        $this->pagination = $model->getPagination();

        $main = $app->input;
        $this->print = $main->get('print');
        $this->filterstring = $main->get('filterstring');

        // Check for errors.
        if (!$this->checkError()) {
            return false;
        }

        // Get the parameters of the active menu item
        $this->params = $app->getParams();
        $this->params->merge($this->item->params);
        $this->params->set('filterstring', $this->filterstring);

        // check if access is not public
        $groups = $user->getAuthorisedViewLevels();

        $this->additional = $this->rows['additional'];
        $this->additional['defaultSorting'] = $this->isDefaultSorted();
        $this->additional['dropdowns'] = $this->buildDropdownJsArray();
        $this->additional['containsDate'] = $this->containsDate();
        $this->rows = $this->rows['rows'];
        if (isset($active->query['layout'])) {
            // We need to set the layout in case this is an alternative menu item (with an alternative layout)
            $this->setLayout($active->query['layout']);
        }

        // Added language variables.
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_LAYOUTMODE');
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_STACK');
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_SWIPE');
        JText::script('COM_EVENTTABLEEDIT_LAYOUT_TOGGLE');

        ob_start();
        $this->_prepareDocument();
        include JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'loadete'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'default.php';
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    private function checkError()
    {
        return true;
    }

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

    protected function _prepareDocument()
    {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $pathway = $app->getPathway();
        $this->document = JFactory::getDocument();
        $this->baseurl = JURI::base();
        $title = null;

        JHTML::_('behavior.tooltip');
        JHTML::_('behavior.calendar');
        JHtml::_('behavior.framework');

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
        $this->document->addStyleSheet($this->baseurl.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'tablesaw.css');
        $this->document->addStyleSheet($this->baseurl.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'eventtablecss.css');
        $this->document->addStyleDeclaration($this->getVariableStyles($this->item->cellspacing, $this->item->cellpadding, $this->item->tablecolor1, $this->item->tablecolor2));
        $this->document->addCustomTag($this->getBrowserStyles());

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
            require JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'loadete'.DIRECTORY_SEPARATOR.'phpToJs.php';
            $doc = JFactory::getDocument();
            $this->document->addScriptDeclaration('
													var newest = "'.JText::_('COM_EVENTTABLEEDIT_NEWEST').'";
													var oldest = "'.JText::_('COM_EVENTTABLEEDIT_OLDEST').'";
													var unique = "'.$this->unique.'";
													
													');
            $this->document->addScript($this->baseurl.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'tablesaw.js?v3');
            $this->document->addScript($this->baseurl.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'tablesaw-init.js');

            require JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'loadete'.DIRECTORY_SEPARATOR.'tableAjax.php';
            require_once JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'loadete'.DIRECTORY_SEPARATOR.'popup.php';
        }
    }

    private function getVariableStyles($cellspacing, $cellpadding, $linecolor0, $linecolor1)
    {
        $style = [];
        $style[] = '#etetable-table td {padding: '.$cellpadding.'px;}';
        $style[] = '@media screen and (min-width: 959px) and  (max-width: 995px) { #etetable-table td {padding: 0px;} }';

        $style[] = '.tablesaw tbody tr:nth-child(odd) {background-color: #'.(($linecolor0)?$linecolor0:'#ffffff').';}';
        $style[] = '.tablesaw tbody tr:nth-child(even) {background-color: #'.(($linecolor1)?$linecolor1:'#ffffff').';}';

        if (0 !== (int) $cellspacing) {
            $style[] = '#etetable-table {border-collapse: separate !important;}';
        }

        $style[] = '.eventtableedit .limit {display: none;}';

        if (0 === $this->item->rowsort) {
            $style[] = '.eventtableedit .sort_col {display: none !important;}';
        }

        if (!$this->params->get('access-reorder')) {
            $style[] = '.eventtableedit .sort_col {display: none !important;}';
        }

        if (!$this->params->get('access-delete')) {
            $style[] = '.eventtableedit .del_col {display: none !important;}';
        }

        return implode("\n", $style);
    }

    private function getBrowserStyles()
    {
        $ie = '<!--[if IE]>'."\n";
        $ie .= '<link rel="stylesheet" href="'.$this->baseurl.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'ie.css" />'."\n";
        $ie .= '<![endif]-->'."\n";

        $ie .= '<!--[if lte IE 7]>'."\n";
        $ie .= '<link rel="stylesheet" href="'.$this->baseurl.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_eventtableedit'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'ie7.css" />'."\n";
        $ie .= '<![endif]-->'."\n";

        return $ie;
    }
}
