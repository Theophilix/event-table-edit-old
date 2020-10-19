<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

class eteHelper
{
    public static function date_german_to_mysql($date)
    {
        $d = explode('.', $date);

        if ('' === $date) {
            return null;
        }

        return    sprintf('%04d.%02d.%02d', $d[2], $d[1], $d[0]);
    }

    public static function date_mysql_to_german($date, $format)
    {
        //if($date == '0000-00-00'){ return '00-00-0000';}
        if (null === $date || '0000-00-00' === $date || '&nbsp;' === $date || '&nbsp' === $date) {
            return null;
        }
        $lang = JFactory::getLanguage();
        if ('de-DE' === $lang->getTag()) {
            setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');
        }
        $hidden = '<input type="hidden" value="'.strtotime($date).'">';
        return  $hidden.utf8_encode(strftime($format, strtotime(str_replace('.', '-', $date))));
    }

    public static function date_mysql_to_german_to($date, $format)
    {
        //if($date == '0000-00-00'){ return '00-00-0000';}
        if (null === $date || '0000-00-00' === $date || '&nbsp;' === $date) {
            return null;
        }
        $lang = JFactory::getLanguage();
        if ('de-DE' === $lang->getTag()) {
            setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');
        }
        return  utf8_encode(strftime($format, strtotime($date)));
    }

    public static function format_time($time, $format)
    {
        if (null === $time) {
            return null;
        }
        $lang = JFactory::getLanguage();
        if ('de-DE' === $lang->getTag()) {
            setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');
        }
        return utf8_encode(strftime($format, strtotime($time)));
    }

    public static function parseBoolean($cell)
    {
        if ('' !== $cell && null !== $cell) {
            if (1 === (int) $cell) {
                $cell = '<img src="'.JURI::root().'components/com_eventtableedit/template/images/cross.png" alt="cross">';
            } elseif (0 === (int) $cell) {
                $cell = '<img src="'.JURI::root().'components/com_eventtableedit/template/images/tick.png"  alt="tick">';
            } else {
                $cell = '';
            }
        }

        return $cell;
    }

    public static function parseFloat($cell, $separator)
    {
        if ('' !== $cell && ',' === $separator) {
            $cell = str_replace('.', ',', $cell);
        }

        return $cell;
    }

    public static function parseLink($cell, $target, $cellbreak)
    {
        if ('' !== $cell) {
            // Add http:// if necessary
            $cellHref = $cell;
            if ('http://' !== substr($cell, 0, 7)) {
                $cellHref = 'http://'.$cell;
            }

            // Spaces at the end, that the cell can be clicked
            $cell = '<a href="'.$cellHref.'" target="'.$target.'">'.eteHelper::breakCell($cell, $cellbreak).'</a>&nbsp;&nbsp;&nbsp;';
        }

        return $cell;
    }

    public static function parseMail($cell, $cellbreak)
    {
        if ('' !== $cell) {
            // Spaces at the end, that the cell can be clicked
            $cell = '<a href="mailto:'.$cell.'">'.eteHelper::breakCell($cell, $cellbreak).'</a>';
        }

        return $cell;
    }

    public static function parseText($cell, $bbcode, $bbcode_img, $link_target_p, $cellbreak)
    {
        if ($bbcode) {
            require JPATH_ROOT.'/components/com_eventtableedit/helpers/bb_code/vendor/autoload.php';
            $code = new \Decoda\Decoda();
            $code->addFilter(new \Decoda\Filter\DefaultFilter());
            $code->addHook(new \Decoda\Hook\CensorHook());
            $code->addFilter(new \Decoda\Filter\BlockFilter());
            $code->addFilter(new \Decoda\Filter\EmailFilter());
            $code->addFilter(new \Decoda\Filter\UrlFilter());
            $code->addHook(new \Decoda\Hook\ClickableHook());
            $code->addFilter(new \Decoda\Filter\CodeFilter());
            $code->addHook(new \Decoda\Hook\EmoticonHook(['path' => JURI::base().'/components/com_eventtableedit/helpers/bb_code/emoticons/']));

            if ($bbcode_img) {
                $code->addFilter(new \Decoda\Filter\ImageFilter());
            }

            $code->addFilter(new \Decoda\Filter\ListFilter());
            $code->addFilter(new \Decoda\Filter\QuoteFilter());
            $code->addFilter(new \Decoda\Filter\TextFilter());
            $code->addFilter(new \Decoda\Filter\VideoFilter());

            $code->reset($cell);
            $cell = $code->parse();
        }
        $cell = eteHelper::breakCell($cell, $cellbreak);

        return $cell;
    }

    private static function breakCell($cell, $cellbreak)
    {
        if (strlen(strip_tags($cell)) > $cellbreak && 0 !== (int)$cellbreak) {
            $cellShort = substr(strip_tags($cell), 0, $cellbreak).'...';
            $cell = JHTML::tooltip($cell, '', '', $cellShort);
        }

        return $cell;
    }

    public static function parseFourState($cell)
    {
        if ('' !== $cell && null !== $cell) {
            if (0 === (int) $cell) {
                $cell = '<img src="'.JURI::root().'components/com_eventtableedit/template/images/tick.png" alt="tick">';
            } elseif (1 === (int) $cell) {
                $cell = '<img src="'.JURI::root().'components/com_eventtableedit/template/images/cross.png" alt="cross">';
            } elseif (2 === (int) $cell) {
                $cell = '<img src="'.JURI::root().'components/com_eventtableedit/template/images/question-mark.png"  alt="question-mark">';
            } else {
                $cell = '';
            }
        }

        return $cell;
    }
}
