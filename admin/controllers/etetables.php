<?php
/**
 * $Id: $.
 *
 * @copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class EventtableeditControllerEtetables extends JControllerAdmin
{
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * Proxy for getModel.
     *
     * @param string $name   the name of the model
     * @param string $prefix the prefix for the PHP class name
     *
     * @return JModel
     *
     * @since	1.6
     */
    public function &getModel($name = 'Etetable', $prefix = 'EventtableeditModel')
    {
        $model = parent::getModel($name, $prefix, ['ignore_request' => true]);

        return $model;
    }
}
