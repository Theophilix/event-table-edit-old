<?php
/**
 * @version
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class EventtableeditControllerEtetable extends JControllerLegacy
{
    /**
     * Get cellcontent.
     */
    public function ajaxGetCell()
    {
        $main = JFactory::getApplication()->input;
        $rowId = $main->getInt('rowId', '-1');
        $tableId = $main->getInt('id', '-1');
        if (!$this->aclCheck('edit') && !$this->checkAclOwnRow($rowId)) {
            return false;
        }

        $postget = $main->getArray();

        $cell = $postget['cell'];

        //Get Model and perform action

        $model = $this->getModel('etetable');
        $ret = $model->getCell($tableId, $rowId, $cell);

        echo $ret;
        exit;
    }

    /**
     * Saves a cellcontent.
     */
    public function ajaxSaveCell()
    {
        $main = JFactory::getApplication()->input;
        $rowId = $main->getInt('rowId', '-1');
        $tableId = $main->getInt('id', '-1');
        if (!$this->aclCheck('edit') && !$this->checkAclOwnRow($rowId)) {
            return false;
        }
        $postget = $main->getArray();

        $cell = $postget['cell'];
        $content = nl2br($postget['content']);

        $db = JFactory::getDBO();
        // START if appointment text changed from appointment view then below code is efected //
        $gettable_settings = "SELECT * FROM #__eventtableedit_details WHERE id='".$postget['id']."'";
        $db->setQuery($gettable_settings);
        $current_table_settings = $db->loadobject();
        //Get Model and perform action
        $model = $this->getModel('etetable');
        $data = $model->saveCell($rowId, $cell, $content, $tableId);
        $ret = $data[0];

        if (1 === (int)$current_table_settings->normalorappointment) {
            $user = JFactory::GetUser();
            if (in_array('8', $user->groups)) {
                $permisioncheck = $current_table_settings->showusernametoadmin;
                $admin = 1;
            } else {
                $permisioncheck = $current_table_settings->showusernametouser;
                $admin = 0;
            }

            if (0 !== (int)$cell) {
                if ('free' === $ret) {
                    $ret = '<span class="blueclass">'.JText::_(strtoupper($ret)).'</span>'; // free appointment
                } else {
                    if (1 === (int)$admin) {
                        if (0 === (int)$permisioncheck) {
                            $ret = 'reserved';
                            $ret = strtoupper($ret);
                        }
                    } else {
                        if (0 === (int)$permisioncheck) {
                            $ret = 'reserved';
                            $ret = strtoupper($ret);
                        }
                    }
                    $ret = '<span class="redclass">'.JText::_($ret).'</span>'; // reserved appointment
                }
            }
        } else {
            $ret = $ret;
        }
        // END if appointment text changed from appointment view then below code is efected //
        if (isset($datatype) && ('boolean' === $datatype || 'four_state' === $datatype)) {
            $pos = strpos($ret, 'cross.png');
            $pos1 = strpos($ret, 'tick.png');
            $pos2 = strpos($ret, 'question-mark.png');
            $atemptime = '';
            if (false !== $pos) {
                $atemptime = '<input type="hidden" value="0">';
            } elseif (false !== $pos1) {
                $atemptime = '<input type="hidden" value="1">';
            } elseif (false !== $pos2) {
                $atemptime = '<input type="hidden" value="2">';
            } else {
                $atemptime = '<input type="hidden" value="-1">';
            }
            echo $atemptime;
        }
        echo $ret.'|'.$data[1];
        exit;
    }

    /**
     * Create a new row through an ajax request.
     */
    public function ajaxNewRow()
    {
        if (!$this->aclCheck('add')) {
            return false;
        }
        $main = JFactory::getApplication()->input;
        $tableId = $main->getInt('id', '-1');
        //Get Model and perform action
        $model = $this->getModel('etetable');
        $ret = $model->newRow($tableId);

        echo $ret;
        exit;
    }

    /**
     * Delete a row through an ajax request.
     */
    public function ajaxDeleteRow()
    {
        $main = JFactory::getApplication()->input;
        $tableId = $main->getInt('id', '-1');
        $rowId = $main->getInt('rowId', '-1');

        if (!$this->aclCheck('delete') && !$this->checkAclOwnRow($rowId)) {
            return false;
        }

        //Get Model and perform action
        $model = $this->getModel('etetable');
        $model->deleteRow($rowId, $tableId);

        exit;
    }

    public function saveOrder()
    {
        if (!$this->aclCheck('reorder')) {
            return false;
        }
        $main = JFactory::getApplication()->input;
        $postget = $main->getArray();

        $rowIds = $postget['rowId'];
        $order = $postget['order'];
        $Itemid = $postget['Itemid'];
        $id = $postget['id'];

        $model = $this->getModel('etetable');
        $model->saveOrder($rowIds, $order);

        $this->setRedirect(JRoute::_('index.php?option=com_eventtableedit&view=etetable&id='.$id.'&Itemid='.$Itemid, false),
                           JText::_('COM_EVENTTABLEEDIT_SUCCESSFUL_REORDER'));
    }

    public function ajaxSaveOrder()
    {
        if (!$this->aclCheck('reorder')) {
            echo false;
            exit;
        }
        $main = JFactory::getApplication()->input;
        $postget = $main->getArray();

        $rowIds = explode(',', $postget['rowId']);
        $order = explode(',', $postget['order']);
        //$Itemid  = $postget['Itemid'];
        $id = $postget['id'];

        $model = $this->getModel('etetable');

        $model->saveOrder($rowIds, $order, $id);

        echo true;
        exit;
    }

    private function aclCheck($object)
    {
        $user = JFactory::getUser();

        $main = JFactory::getApplication()->input;
        $id = $main->getInt('id', '-1');
        $asset = 'com_eventtableedit.etetable.'.$id;

        if (!$user->authorise('core.'.$object, $asset)) {
            return false;
        }
        return true;
    }

    /**
     * Check if a user created a row himself and
     * has the right to edit it.
     */
    private function checkAclOwnRow($rowId)
    {
        $user = JFactory::getUser();
        $uid = $user->get('id');

        $model = &$this->getModel('etetable');
        return $model->checkAclOwnRow($rowId, $uid);
    }

    public function setSessionOption()
    {
        $jinput = JFactory::getApplication()->input;
        $session = JFactory::getSession();
        $session->set('corresponding_table', $jinput->get('corresponding_table'));
    }
}
