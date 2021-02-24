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

class EventtableeditControllerCsvexport extends JControllerLegacy
{
    protected $text_prefix = 'COM_EVENTTABLEEDIT_CSVEXPORT';
    protected $app;

    protected $id;
    protected $separator;
    protected $doubleqt;
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->app = JFactory::getApplication();
    }

    /**
     * Task that is called when exporting a table.
     */
    public function export()
    {
        // ACL Check
        $user = JFactory::getUser();
		

        // Initialize Variables
        $this->model = $this->getModel('csvexport');

        $input = JFactory::getApplication()->input;
        $postget = $input->getArray();

        $this->id = $postget['tableList'];
		
		$asset = 'com_eventtableedit.etetable.'.$this->id;
        if (!$user->authorise('core.csv', $asset)) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            $this->setRedirect(JRoute('index.php?option=com_eventtableedit'));
            return false;
        }
		
		
        $this->separator = $postget['separator'];
        $this->doubleqt = $postget['doubleqt'];
        $this->csvexporttimestamp = $postget['csvexporttimestamp'];

        //$input->set('com_eventtableedit.layout', 'summary');
        //$input->set('view', 'csvexport');

        $this->model->setVariables($this->id, $this->separator, $this->doubleqt, $this->csvexporttimestamp);
        $this->model->export();
		$this->download();
        //parent::display();
    }

    public function cancel()
    {
        $this->setRedirect(JRoute::_('index.php?option=com_eventtableedit'));
        return false;
    }

    public function download()
    {
        $app = JFactory::getApplication();
        $id = $app->input->get('tableList');
        $file = JPATH_ROOT.'/components/com_eventtableedit/template/tablexml/csv_'.$id.'.csv';

        header('Content-Description: File Transfer');
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($file));
        readfile($file);
        exit;
    }
}
