<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class JHTMLIcon
{
	static function print_popup($article, $params, $attribs = array())
	{
		$url  = 'index.php?option=com_eventtableedit&id='.$article->slug;
		$url .= '&tmpl=component&print=1&view=etetable&layout=print';
		$url .= '&limit=0&limitstart=0&filterstring=' . $params->get('filterstring');

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		$text = JHTML::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);

		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']		= 'nofollow';

		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}

	static function print_screen($article, $params, $attribs = array())
	{
		// checks template image directory for image, if non found default are loaded
		$text = JHTML::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
		
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

	static function adminTable($article, $text) {
		$url  = 'index.php?option=com_eventtableedit&view=changetable&id='.$article->slug;
		
		// checks template image directory for image, if non found default are loaded
		$button = JHTML::_('image','system/edit.png', $text, NULL, true);
		
		$attribs['title'] = $text;

		return JHTML::_('link', JRoute::_($url), $button, $attribs);
	}
}
