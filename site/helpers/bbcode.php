<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

class eteBBCode {
	public $parsebbcode;
	
	public function addbbcode($bbimg) {
		require_once JPATH_ROOT.'/components/com_eventtableedit/helpers/bb_code/stringparser_bbcode.class.php';
		
		$this->parsebbcode = new StringParser_bbcode();
		
		$this->parsebbcode->addCode ('br', 'simple_replace', null, array('start_tag' => '<br>', 'end_tag' => ''),
						'inline', array ('block', 'inline'), array ());
		$this->parsebbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
	                  'inline', array ('block', 'inline'), array ());
	    //##
	    $this->parsebbcode->addCode ('s', 'simple_replace', null, array ('start_tag' => '<s>', 'end_tag' => '</s>'),
	                  'inline', array ('block', 'inline'), array ());
	   	$this->parsebbcode->addCode ('code', 'simple_replace', null, array ('start_tag' => '<code>', 'end_tag' => '</code>'),
	                  'inline', array ('block', 'inline'), array ());
	   	$this->parsebbcode->addCode ('pre', 'simple_replace', null, array ('start_tag' => '<pre>', 'end_tag' => '</pre>'),
	                  'inline', array ('block', 'inline'), array ());
	    $this->parsebbcode->addCode ('left', 'usecontent?', 'do_bbcode_left', array ('usecontent_param' => 'default'), 
				'left', array ('block', 'inline'), array ('link'));
		$this->parsebbcode->addCode ('right', 'usecontent?', 'do_bbcode_right', array ('usecontent_param' => 'default'), 
				'right', array ('block', 'inline'), array ('link'));
		$this->parsebbcode->addCode ('quote', 'usecontent?', 'do_bbcode_quote', array ('usecontent_param' => 'default'), 
				'quote', array ('block', 'inline'), array ('link'));
		
		$this->parsebbcode->addCode ('youtube', 'usecontent?', 'do_bbcode_youtube', array ('usecontent_param' => 'default'), 
				'youtube', array ('block', 'inline'), array ('link'));
		$this->parsebbcode->addCode ('style', 'usecontent?', 'do_bbcode_style', array ('usecontent_param' => 'default'), 
			'style', array ('block', 'inline'), array ('link'));  
		$this->parsebbcode->addCode ('size', 'usecontent?', 'do_bbcode_size', array ('usecontent_param' => 'default'), 
			'size', array ('block', 'inline'), array ('link'));  
		
		//table
		$this->parsebbcode->addCode ('table', 'simple_replace', null, array('start_tag' => '<table>', 'end_tag' => '</table>'), 'inline', array ('block', 'inline'), array ());
		//$this->parsebbcode->addCode ('table', 'simple_replace', null, array('start_tag' => '<table>', 'end_tag' => '</table>'), 'inline', array ('block', 'inline'), array ());
		$this->parsebbcode->addCode ('tr', 'simple_replace', null, array('start_tag' => '<tr>', 'end_tag' => '</tr>'), 'inline', array ('block', 'inline'), array ());
		$this->parsebbcode->addCode ('th', 'simple_replace', null, array('start_tag' => '<th>', 'end_tag' => '</th>'), 'inline', array ('block', 'inline'), array ());
		$this->parsebbcode->addCode ('td', 'simple_replace', null, array('start_tag' => '<td>', 'end_tag' => '</td>'), 'inline', array ('block', 'inline'), array ());
		
		

		$this->parsebbcode->addCode ('li', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
	                  'inline', array ('block', 'inline'), array ());
		$this->parsebbcode->addCode ('ul', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
	                  'inline', array ('block', 'inline'), array ());
	    $this->parsebbcode->addCode ('ol', 'simple_replace', null, array ('start_tag' => '<ol>', 'end_tag' => '</ol>'),
	                  'inline', array ('block', 'inline'), array ());
	    
		$this->parsebbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'),
	                  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parsebbcode->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'),
                	'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parsebbcode->addCode ('center', 'simple_replace', null, array ('start_tag' => '<center>', 'end_tag' => '</center>'),
                	'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parsebbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
					  'link', array ('listitem', 'block', 'inline'), array ('link'));
		$this->parsebbcode->addCode ('color', 'usecontent?', 'do_bbcode_color', array ('usecontent_param' => 'default'), 
			'link', array ('block', 'inline'), array ('link'));  
		$this->parsebbcode->addCode ('link', 'callback_replace_single', 'do_bbcode_url', array (),
					  'link', array ('listitem', 'block', 'inline'), array ('link'));	
		$this->parsebbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
	                  'inline', array ('block', 'inline'), array ());
		$this->parsebbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
	                  'listitem', array ('list'), array ());
	    
		$this->parsebbcode->setCodeFlag ('*', 'closetag', 'bbcode_CLOSETAG_OPTIONAL');
		$this->parsebbcode->setCodeFlag ('*', 'paragraphs', false);
		$this->parsebbcode->setCodeFlag ('list', 'paragraph_type', 'bbcode_PARAGRAPH_BLOCK_ELEMENT');
		$this->parsebbcode->setCodeFlag ('list', 'opentag.before.newline', 'bbcode_NEWLINE_DROP');
		$this->parsebbcode->setCodeFlag ('list', 'closetag.before.newline', 'bbcode_NEWLINE_DROP');
			
		//Optional img
		if ($bbimg) {
			$this->parsebbcode->addCode ('img', 'usecontent?', 'do_bbcode_img', array (), 'image', array ('listitem', 'block', 'inline', 'link'), array ());
			$this->parsebbcode->setOccurrenceType ('img', 'image');
		}
	}
}

function do_bbcode_left($action, $attributes, $content, $params, $node_object) {
	if ($action == 'validate') {
	    return true;
	}
	return '<p style="text-align: left;">' . $content . '</p>';
}
function do_bbcode_right($action, $attributes, $content, $params, $node_object) {
	if ($action == 'validate') {
	    return true;
	}
	return '<p style="text-align: right;">' . $content . '</p>';
}
function do_bbcode_quote($action, $attributes, $content, $params, $node_object) {
	if ($action == 'validate') {
	    return true;
	}
	if (isset($attributes['default'])) {
		$content= '<strong>'.$attributes['default'].' wrote: </strong>'.$content;
	}
	return '<div style="background-color: #DDDDDD;margin-left: 2em;margin-right: 2em;padding: 5px;" class="quote">'.$content.'</div>';
}
function do_bbcode_youtube($action, $attributes, $content, $params, $node_object) {
	if ($action == 'validate') {
	    return true;
	}
	return '<object width="400" height="325"><param name="movie" value="https://www.youtube.com/v/{param}"></param><embed src="https://www.youtube.com/v/"'.$content.'" type="application/x-shockwave-flash" width="400" height="325"></embed></object><br />';
}

function do_bbcode_url ($action, $attributes, $content, $params, $node_object) {
    global $link_target;

    if (!isset ($attributes['default'])) {
		$url = $content;
		$text = htmlspecialchars ($content);
	} else {
		$url = $attributes['default'];
		$text = $content;
	}
	if ($action == 'validate') {
		if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
		|| substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
			return false;
		}
		return true;
	}
	return '<a href="'.htmlspecialchars ($url).'" target="' . $link_target . '">'.$text.'</a>';
}

function do_bbcode_img ($action, $attributes, $content, $params, $node_object) {
	if ($action == 'validate') {
		if (substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:'
		|| substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:') {
			return false;
		}
		return true;
	}
	$attr='';
	if (isset ($attributes['default'])) {
		$arr = explode('x', $attributes['default']);
		if($arr[0]!='') $attr.=' width="'.$arr[0].'px"';
		if($arr[1]!='') $attr.=' height="'.$arr[1].'px"';
	}
	if($attributes['width']!=''){
		$attr.=' width="'.$attributes['width'].'"';
	}
	if($attributes['height']!=''){
		$attr.='height="'.$attributes['height'].'"';
	}
	if($attributes['alt']!=''){
		$attr.='alt="'.$attributes['alt'].'"';
	}
	if($attributes['title']!=''){
		$attr.='title="'.$attributes['title'].'"';
	}
	
	return '<img '.$attr.' src="'.htmlspecialchars($content).'" alt="bbcodeimg">';
}
function do_bbcode_size ($action, $attributes, $content, $params, $node_object) {
	if (!isset ($attributes['default'])) {
		return $content;
	}
	if ($action == 'validate') {
	    return true;
	}
	return '<span style="font-size:' . $attributes['default'] . 'px;">' . $content . '</span>';
}
function do_bbcode_style ($action, $attributes, $content, $params, $node_object) {
	if ($action == 'validate') {
	    return true;
	}
	$attr=array();
	if (isset($attributes['color'])) {
		$attr[] = 'color:'.$attributes['color'];
	}
	if ( $attributes['size']!='') {
		$attr[] = 'font-size:'.$attributes['size'];
	}
	return '<span style='.implode(';',$attr).'>' . $content . '</span>';
}
function do_bbcode_color ($action, $attributes, $content, $params, $node_object) {
	if (!isset ($attributes['default'])) {
		return $content;
	}
	if ($action == 'validate') {
	    return true;
	}
	
	return '<span style="color:' . $attributes['default'] . ';">' . $content . '</span>';
}

