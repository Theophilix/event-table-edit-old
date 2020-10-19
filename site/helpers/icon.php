<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class JHTMLIcon
{
    public static function print_popup($article, $params, $attribs = [])
    {
        $url = 'index.php?option=com_eventtableedit&id='.$article->slug;
        $url .= '&tmpl=component&print=1&view=etetable&layout=print';
        $url .= '&limit=0&limitstart=0&filterstring='.$params->get('filterstring');

        $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

        // checks template image directory for image, if non found default are loaded
        $text = JHTML::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);

        $attribs['title'] = JText::_('JGLOBAL_PRINT');
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
        $attribs['rel'] = 'nofollow';

        return JHTML::_('link', JRoute::_($url), $text, $attribs);
    }

    public static function print_screen($article, $params, $attribs = [])
    {
        // checks template image directory for image, if non found default are loaded
        $text = JHTML::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);

        return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
    }

    public static function adminTable($article, $text)
    {
        $url = 'index.php?option=com_eventtableedit&view=changetable&id='.$article->slug;

        // checks template image directory for image, if non found default are loaded
        $button = JHTML::_('image', 'system/edit.png', $text, null, true);

        $attribs['title'] = $text;

        return JHTML::_('link', JRoute::_($url), $button, $attribs);
    }
}
