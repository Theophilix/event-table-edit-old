<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

class EventtableeditControllerXmlimport extends JControllerLegacy
{
    protected $app;

    protected $id;
    protected $file;
    protected $importaction;
    protected $separator;
    protected $doubleqt;
    protected $checkfun;
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->app = JFactory::getApplication();
    }

    /**
     * Task that is called when uploading a csv file.
     */
    public function upload()
    {
        // ACL Check

        $app = JFactory::getApplication();

        $input = JFactory::getApplication()->input;
        $postget = $input->getArray();

        $xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/eventtableedit.xml');
        $currentversion = (string) $xml->version;

        // Initialize Variables
        $this->model = $this->getModel('xmlimport');
        $this->file = $input->files->get('fupload');
        $this->checkfun = @$postget['checkfun'];
        $info = pathinfo(basename($this->file['name']));
        $ext = strtolower($info['extension']);

        if (!$this->file['name']) {
            $msg = JTEXT::_('COM_EVENTTABLEEDIT_UPLOAD_XMLFILE_VALID');
            $app->redirect('index.php?option=com_eventtableedit&view=xmlimport', $msg, 'error');
        }
        if ('xml' !== $ext) {
            $msg = JTEXT::_('COM_EVENTTABLEEDIT_UPLOAD_XMLFILE_VALID');
            $app->redirect('index.php?option=com_eventtableedit&view=xmlimport', $msg, 'error');
        }

        $xml = simplexml_load_file($this->file['tmp_name']);
        if (empty($xml)) {
            $msg = JTEXT::_('COM_EVENTTABLEEDIT_FILE_IS_NOT_CORRECT');
            $app->redirect('index.php?option=com_eventtableedit&view=xmlimport', $msg, 'error');
        } elseif ('Event_Table_Edit_XML_file' !== $xml->getName()) {
            $msg = JTEXT::_('COM_EVENTTABLEEDIT_FILE_IS_NOT_CORRECT');
            $app->redirect('index.php?option=com_eventtableedit&view=xmlimport', $msg, 'error');
        }
        $xml = json_encode($xml);
        $xml = json_decode($xml, true);

        $xml['id'] = 0;
        if (count($xml['rowdata']['linerow']) > 0) {
            $xml['temps'] = 0;
        } else {
            $xml['temps'] = 1;
        }
        $xml['alias'] = substr(md5(rand()), 0, 7);
        $xml['checkfun'] = $this->checkfun ? $this->checkfun : '0';

        $model = $this->getModel('Etetable', 'EventtableeditModel');

        $tablesave = $model->saveXml($xml);
        //exit;
        if ($tablesave > 0) {
            $url = 'index.php?option=com_eventtableedit&view=etetables';
            if ($xml['normalorappointment']) {
                $url = 'index.php?option=com_eventtableedit&view=appointmenttables';
            }
            $msg = JTEXT::_('COM_EVENTTABLEEDIT_SUCCESSFULLY_TABLES_AND_DATA_CREATED');
            if ($currentversion !== $xml['ETE_version']) {
                $msg = JTEXT::_('COM_EVENTTABLEEDIT_FILE_IMPORTED_BUT_ETE_VERSION_NOT_MATCH');
                $app->redirect($url, $msg, JTEXT::_('COM_EVENTTABLEEDIT_FILE_IMPORTED_WARNING'));
            }
            $app->redirect($url, $msg);
        }

        parent::display();
    }

    public function cancel()
    {
        $this->setRedirect(JRoute::_('index.php?option=com_eventtableedit'));
        return false;
    }
}
