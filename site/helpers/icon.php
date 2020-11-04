<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

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
	
	public static function edit($article, $params, $attribs = array(), $legacy = false)
	{
		$user = JFactory::getUser();
		$uri  = JUri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($article->state < 0)
		{
			return;
		}

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($article, 'checked_out')
			&& property_exists($article, 'checked_out_time')
			&& $article->checked_out > 0
			&& $article->checked_out != $user->get('id'))
		{
			$checkoutUser = JFactory::getUser($article->checked_out);
			$date         = JHtml::_('date', $article->checked_out_time);
			$tooltip      = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . JText::sprintf('COM_CONTENT_CHECKED_OUT_BY', $checkoutUser->name)
				. ' <br /> ' . $date;

			$text = JLayoutHelper::render('joomla.content.icons.edit_lock', array('tooltip' => $tooltip, 'legacy' => $legacy));

			$output = JHtml::_('link', '#', $text, $attribs);

			return $output;
		}

		$contentUrl = ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language);
		$url        = $contentUrl . '&task=article.edit&a_id=' . $article->id . '&return=' . base64_encode($uri);

		if ($article->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date   = JHtml::_('date', $article->created);
		$author = $article->created_by_alias ?: $article->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_CONTENT_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$text = JLayoutHelper::render('joomla.content.icons.edit', array('article' => $article, 'overlib' => $overlib, 'legacy' => $legacy));

		$attribs['title']   = JText::_('JGLOBAL_EDIT_TITLE');
		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

		return $output;
	}
}
