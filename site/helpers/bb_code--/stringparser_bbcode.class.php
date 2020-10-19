<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once dirname(__FILE__).'/stringparser.class.php';

define('BBCODE_CLOSETAG_FORBIDDEN', -1);
define('BBCODE_CLOSETAG_OPTIONAL', 0);
define('BBCODE_CLOSETAG_IMPLICIT', 1);
define('BBCODE_CLOSETAG_IMPLICIT_ON_CLOSE_ONLY', 2);
define('BBCODE_CLOSETAG_MUSTEXIST', 3);

define('BBCODE_NEWLINE_PARSE', 0);
define('BBCODE_NEWLINE_IGNORE', 1);
define('BBCODE_NEWLINE_DROP', 2);

define('BBCODE_PARAGRAPH_ALLOW_BREAKUP', 0);
define('BBCODE_PARAGRAPH_ALLOW_INSIDE', 1);
define('BBCODE_PARAGRAPH_BLOCK_ELEMENT', 2);

/**
 * BB code string parser class.
 */
class StringParser_BBCode extends StringParser
{
    /**
     * String parser mode.
     *
     * The BBCode string parser works in search mode
     *
     * @var int
     *
     * @see STRINGPARSER_MODE_SEARCH, STRINGPARSER_MODE_LOOP
     */
    public $_parserMode = STRINGPARSER_MODE_SEARCH;

    /**
     * Defined BB Codes.
     *
     * The registered BB codes
     *
     * @var array
     */
    public $_codes = [];

    /**
     * Registered parsers.
     *
     * @var array
     */
    public $_parsers = [];

    /**
     * Defined maximum occurrences.
     *
     * @var array
     */
    public $_maxOccurrences = [];

    /**
     * Root content type.
     *
     * @var string
     */
    public $_rootContentType = 'block';

    /**
     * Do not output but return the tree.
     *
     * @var bool
     */
    public $_noOutput = false;

    /**
     * Global setting: case sensitive.
     *
     * @var bool
     */
    public $_caseSensitive = true;

    /**
     * Root paragraph handling enabled.
     *
     * @var bool
     */
    public $_rootParagraphHandling = false;

    /**
     * Paragraph handling parameters.
     *
     * @var array
     */
    public $_paragraphHandling = [
        'detect_string' => "\n\n",
        'start_tag' => '<p>',
        'end_tag' => "</p>\n",
    ];

    /**
     * Allow mixed attribute types (e.g. [code=bla attr=blub]).
     *
     * @var bool
     */
    public $_mixedAttributeTypes = false;

    /**
     * Whether to call validation function again (with $action == 'validate_auto') when closetag comes.
     *
     * @var bool
     */
    public $_validateAgain = false;

    /**
     * Add a code.
     *
     * @param string $name               The name of the code
     * @param string $callback_type      See documentation
     * @param string $callback_func      The callback function to call
     * @param array  $callback_params    The callback parameters
     * @param string $content_type       See documentation
     * @param array  $allowed_within     See documentation
     * @param array  $not_allowed_within See documentation
     *
     * @return bool
     */
    public function addCode($name, $callback_type, $callback_func, $callback_params, $content_type, $allowed_within, $not_allowed_within)
    {
        if (isset($this->_codes[$name])) {
            return false; // already exists
        }
        if (!preg_match('/^[a-zA-Z0-9*_!+-]+$/', $name)) {
            return false; // invalid
        }
        $this->_codes[$name] = [
            'name' => $name,
            'callback_type' => $callback_type,
            'callback_func' => $callback_func,
            'callback_params' => $callback_params,
            'content_type' => $content_type,
            'allowed_within' => $allowed_within,
            'not_allowed_within' => $not_allowed_within,
            'flags' => [],
        ];
        return true;
    }

    /**
     * Remove a code.
     *
     * @param $name The code to remove
     *
     * @return bool
     */
    public function removeCode($name)
    {
        if (isset($this->_codes[$name])) {
            unset($this->_codes[$name]);
            return true;
        }
        return false;
    }

    /**
     * Remove all codes.
     */
    public function removeAllCodes()
    {
        $this->_codes = [];
    }

    /**
     * Set a code flag.
     *
     * @param string $name  The name of the code
     * @param string $flag  The name of the flag to set
     * @param mixed  $value The value of the flag to set
     *
     * @return bool
     */
    public function setCodeFlag($name, $flag, $value)
    {
        if (!isset($this->_codes[$name])) {
            return false;
        }
        $this->_codes[$name]['flags'][$flag] = $value;
        return true;
    }

    /**
     * Set occurrence type.
     *
     * Example:
     *   $bbcode->setOccurrenceType ('url', 'link');
     *   $bbcode->setMaxOccurrences ('link', 4);
     * Would create the situation where a link may only occur four
     * times in the hole text.
     *
     * @param string $code The name of the code
     * @param string $type The name of the occurrence type to set
     *
     * @return bool
     */
    public function setOccurrenceType($code, $type)
    {
        return $this->setCodeFlag($code, 'occurrence_type', $type);
    }

    /**
     * Set maximum number of occurrences.
     *
     * @param string $type  The name of the occurrence type
     * @param int    $count The maximum number of occurrences
     *
     * @return bool
     */
    public function setMaxOccurrences($type, $count)
    {
        settype($count, 'integer');
        if ($count < 0) { // sorry, does not make any sense
            return false;
        }
        $this->_maxOccurrences[$type] = $count;
        return true;
    }

    /**
     * Add a parser.
     *
     * @param string $type   The content type for which the parser is to add
     * @param mixed  $parser The function to call
     *
     * @return bool
     */
    public function addParser($type, $parser)
    {
        if (is_array($type)) {
            foreach ($type as $t) {
                $this->addParser($t, $parser);
            }
            return true;
        }
        if (!isset($this->_parsers[$type])) {
            $this->_parsers[$type] = [];
        }
        $this->_parsers[$type][] = $parser;
        return true;
    }

    /**
     * Set root content type.
     *
     * @param string $content_type The new root content type
     */
    public function setRootContentType($content_type)
    {
        $this->_rootContentType = $content_type;
    }

    /**
     * Set paragraph handling on root element.
     *
     * @param bool $enabled The new status of paragraph handling on root element
     */
    public function setRootParagraphHandling($enabled)
    {
        $this->_rootParagraphHandling = (bool) $enabled;
    }

    /**
     * Set paragraph handling parameters.
     *
     * @param string $detect_string The string to detect
     * @param string $start_tag     The replacement for the start tag (e.g. <p>)
     * @param string $end_tag       The replacement for the start tag (e.g. </p>)
     */
    public function setParagraphHandlingParameters($detect_string, $start_tag, $end_tag)
    {
        $this->_paragraphHandling = [
            'detect_string' => $detect_string,
            'start_tag' => $start_tag,
            'end_tag' => $end_tag,
        ];
    }

    /**
     * Set global case sensitive flag.
     *
     * If this is set to true, the class normally is case sensitive, but
     * the case_sensitive code flag may override this for a single code.
     *
     * If this is set to false, all codes are case insensitive.
     *
     * @param bool $caseSensitive
     */
    public function setGlobalCaseSensitive($caseSensitive)
    {
        $this->_caseSensitive = (bool) $caseSensitive;
    }

    /**
     * Get global case sensitive flag.
     *
     * @return bool
     */
    public function globalCaseSensitive()
    {
        return $this->_caseSensitive;
    }

    /**
     * Set mixed attribute types flag.
     *
     * If set, [code=val1 attr=val2] will cause 2 attributes to be parsed:
     * 'default' will have value 'val1', 'attr' will have value 'val2'.
     * If not set, only one attribute 'default' will have the value
     * 'val1 attr=val2' (the default and original behaviour)
     *
     * @param bool $mixedAttributeTypes
     */
    public function setMixedAttributeTypes($mixedAttributeTypes)
    {
        $this->_mixedAttributeTypes = (bool) $mixedAttributeTypes;
    }

    /**
     * Get mixed attribute types flag.
     *
     * @return bool
     */
    public function mixedAttributeTypes()
    {
        return $this->_mixedAttributeTypes;
    }

    /**
     * Set validate again flag.
     *
     * If this is set to true, the class calls the validation function
     * again with $action == 'validate_again' when closetag comes.
     *
     * @param bool $validateAgain
     */
    public function setValidateAgain($validateAgain)
    {
        $this->_validateAgain = (bool) $validateAgain;
    }

    /**
     * Get validate again flag.
     *
     * @return bool
     */
    public function validateAgain()
    {
        return $this->_validateAgain;
    }

    /**
     * Get a code flag.
     *
     * @param string $name    The name of the code
     * @param string $flag    The name of the flag to get
     * @param string $type    The type of the return value
     * @param mixed  $default The default return value
     *
     * @return bool
     */
    public function getCodeFlag($name, $flag, $type = 'mixed', $default = null)
    {
        if (!isset($this->_codes[$name])) {
            return $default;
        }
        if (!array_key_exists($flag, $this->_codes[$name]['flags'])) {
            return $default;
        }
        $return = $this->_codes[$name]['flags'][$flag];
        if ('mixed' !== $type) {
            settype($return, $type);
        }
        return $return;
    }

    /**
     * Set a specific status.
     */
    public function _setStatus($status)
    {
        switch ($status) {
            case 0:
                $this->_charactersSearch = ['[/', '['];
                $this->_status = $status;
                break;
            case 1:
                $this->_charactersSearch = [']', ' = "', '="', ' = \'', '=\'', ' = ', '=', ': ', ':', ' '];
                $this->_status = $status;
                break;
            case 2:
                $this->_charactersSearch = [']'];
                $this->_status = $status;
                $this->_savedName = '';
                break;
            case 3:
                if (null !== $this->_quoting) {
                    if ($this->_mixedAttributeTypes) {
                        $this->_charactersSearch = ['\\\\', '\\'.$this->_quoting, $this->_quoting.' ', $this->_quoting.']', $this->_quoting];
                    } else {
                        $this->_charactersSearch = ['\\\\', '\\'.$this->_quoting, $this->_quoting.']', $this->_quoting];
                    }
                    $this->_status = $status;
                    break;
                }
                if ($this->_mixedAttributeTypes) {
                    $this->_charactersSearch = [' ', ']'];
                } else {
                    $this->_charactersSearch = [']'];
                }
                $this->_status = $status;
                break;
            case 4:
                $this->_charactersSearch = [' ', ']', '="', '=\'', '='];
                $this->_status = $status;
                $this->_savedName = '';
                $this->_savedValue = '';
                break;
            case 5:
                if (null !== $this->_quoting) {
                    $this->_charactersSearch = ['\\\\', '\\'.$this->_quoting, $this->_quoting.' ', $this->_quoting.']', $this->_quoting];
                } else {
                    $this->_charactersSearch = [' ', ']'];
                }
                $this->_status = $status;
                $this->_savedValue = '';
                break;
            case 7:
                $this->_charactersSearch = ['[/'.$this->_topNode('name').']'];
                if (!$this->_topNode('getFlag', 'case_sensitive', 'boolean', true) || !$this->_caseSensitive) {
                    $this->_charactersSearch[] = '[/';
                }
                $this->_status = $status;
                break;
            default:
                return false;
        }
        return true;
    }

    /**
     * Abstract method Append text depending on current status.
     *
     * @param string $text The text to append
     *
     * @return bool On success, the function returns true, else false
     */
    public function _appendText($text)
    {
        if (!strlen($text)) {
            return true;
        }
        switch ($this->_status) {
            case 0:
            case 7:
                return $this->_appendToLastTextChild($text);
            case 1:
                return $this->_topNode('appendToName', $text);
            case 2:
            case 4:
                $this->_savedName .= $text;
                return true;
            case 3:
                return $this->_topNode('appendToAttribute', 'default', $text);
            case 5:
                $this->_savedValue .= $text;
                return true;
            default:
                return false;
        }
    }

    /**
     * Restart parsing after current block.
     *
     * To achieve this the current top stack object is removed from the
     * tree. Then the current item
     *
     * @return bool
     */
    public function _reparseAfterCurrentBlock()
    {
        if (2 === $this->_status) {
            // this status will *never* call _reparseAfterCurrentBlock itself
            // so this is called if the loop ends
            // therefore, just add the [/ to the text

            // _savedName should be empty but just in case
            $this->_cpos -= strlen($this->_savedName);
            $this->_savedName = '';
            $this->_status = 0;
            $this->_appendText('[/');
            return true;
        } else {
            return parent::_reparseAfterCurrentBlock();
        }
    }

    /**
     * Apply parsers.
     */
    public function _applyParsers($type, $text)
    {
        if (!isset($this->_parsers[$type])) {
            return $text;
        }
        foreach ($this->_parsers[$type] as $parser) {
            if (is_callable($parser)) {
                $ntext = call_user_func($parser, $text);
                if (is_string($ntext)) {
                    $text = $ntext;
                }
            }
        }
        return $text;
    }

    /**
     * Handle status.
     *
     * @param int    $status The current status
     * @param string $needle The needle that was found
     *
     * @return bool
     */
    public function _handleStatus($status, $needle)
    {
        switch ($status) {
            case 0: // NORMAL TEXT
                if ('[' !== $needle && '[/' !== $needle) {
                    $this->_appendText($needle);
                    return true;
                }
                if ('[' === $needle) {
                    $node = new StringParser_BBCode_Node_Element($this->_cpos);
                    $res = $this->_pushNode($node);
                    if (!$res) {
                        return false;
                    }
                    $this->_setStatus(1);
                } elseif ('[/' === $needle) {
                    if (count($this->_stack) <= 1) {
                        $this->_appendText($needle);
                        return true;
                    }
                    $this->_setStatus(2);
                }
                break;
            case 1: // OPEN TAG
                if (']' === $needle) {
                    return $this->_openElement(0);
                } elseif (':' === trim($needle) || '=' === trim($needle)) {
                    $this->_quoting = null;
                    $this->_setStatus(3); // default value parser
                    break;
                } elseif ('="' === trim($needle) || '= "' === trim($needle) || '=\'' === trim($needle) || '= \'' === trim($needle)) {
                    $this->_quoting = substr(trim($needle), -1);
                    $this->_setStatus(3); // default value parser with quotation
                    break;
                } elseif (' ' === $needle) {
                    $this->_setStatus(4); // attribute parser
                    break;
                } else {
                    $this->_appendText($needle);
                    return true;
                }
                // break not necessary because every if clause contains return
                // no break
            case 2: // CLOSE TAG
                if (']' !== $needle) {
                    $this->_appendText($needle);
                    return true;
                }
                $closecount = 0;
                if (!$this->_isCloseable($this->_savedName, $closecount)) {
                    $this->_setStatus(0);
                    $this->_appendText('[/'.$this->_savedName.$needle);
                    return true;
                }
                // this validates the code(s) to be closed after the content tree of
                // that code(s) are built - if the second validation fails, we will have
                // to reparse. note that as _reparseAfterCurrentBlock will not work correctly
                // if we're in $status == 2, we will have to set our status to 0 manually
                if (!$this->_validateCloseTags($closecount)) {
                    $this->_setStatus(0);
                    return $this->_reparseAfterCurrentBlock();
                }
                $this->_setStatus(0);
                for ($i = 0; $i < $closecount; ++$i) {
                    if ($i === $closecount - 1) {
                        $this->_topNode('setHadCloseTag');
                    }
                    if (!$this->_popNode()) {
                        return false;
                    }
                }
                break;
            case 3: // DEFAULT ATTRIBUTE
                if (null !== $this->_quoting) {
                    if ('\\\\' === $needle) {
                        $this->_appendText('\\');
                        return true;
                    } elseif ($needle === '\\'.$this->_quoting) {
                        $this->_appendText($this->_quoting);
                        return true;
                    } elseif ($needle === $this->_quoting.' ') {
                        $this->_setStatus(4);
                        return true;
                    } elseif ($needle === $this->_quoting.']') {
                        return $this->_openElement(2);
                    } elseif ($needle === $this->_quoting) {
                        // can't be, only ']' and ' ' allowed after quoting char
                        return $this->_reparseAfterCurrentBlock();
                    } else {
                        $this->_appendText($needle);
                        return true;
                    }
                } else {
                    if (' ' === $needle) {
                        $this->_setStatus(4);
                        return true;
                    } elseif (']' === $needle) {
                        return $this->_openElement(2);
                    } else {
                        $this->_appendText($needle);
                        return true;
                    }
                }
                // break not needed because every if clause contains return!
                // no break
            case 4: // ATTRIBUTE NAME
                if (' ' === $needle) {
                    if (strlen($this->_savedName)) {
                        $this->_topNode('setAttribute', $this->_savedName, true);
                    }
                    // just ignore and continue in same mode
                    $this->_setStatus(4); // reset parameters
                    return true;
                } elseif (']' === $needle) {
                    if (strlen($this->_savedName)) {
                        $this->_topNode('setAttribute', $this->_savedName, true);
                    }
                    return $this->_openElement(2);
                } elseif ('=' === $needle) {
                    $this->_quoting = null;
                    $this->_setStatus(5);
                    return true;
                } elseif ('="' === $needle) {
                    $this->_quoting = '"';
                    $this->_setStatus(5);
                    return true;
                } elseif ('=\'' === $needle) {
                    $this->_quoting = '\'';
                    $this->_setStatus(5);
                    return true;
                } else {
                    $this->_appendText($needle);
                    return true;
                }
                // break not needed because every if clause contains return!
                // no break
            case 5: // ATTRIBUTE VALUE
                if (null !== $this->_quoting) {
                    if ('\\\\' === $needle) {
                        $this->_appendText('\\');
                        return true;
                    } elseif ($needle === '\\'.$this->_quoting) {
                        $this->_appendText($this->_quoting);
                        return true;
                    } elseif ($needle === $this->_quoting.' ') {
                        $this->_topNode('setAttribute', $this->_savedName, $this->_savedValue);
                        $this->_setStatus(4);
                        return true;
                    } elseif ($needle === $this->_quoting.']') {
                        $this->_topNode('setAttribute', $this->_savedName, $this->_savedValue);
                        return $this->_openElement(2);
                    } elseif ($needle === $this->_quoting) {
                        // can't be, only ']' and ' ' allowed after quoting char
                        return $this->_reparseAfterCurrentBlock();
                    } else {
                        $this->_appendText($needle);
                        return true;
                    }
                } else {
                    if (' ' === $needle) {
                        $this->_topNode('setAttribute', $this->_savedName, $this->_savedValue);
                        $this->_setStatus(4);
                        return true;
                    } elseif (']' === $needle) {
                        $this->_topNode('setAttribute', $this->_savedName, $this->_savedValue);
                        return $this->_openElement(2);
                    } else {
                        $this->_appendText($needle);
                        return true;
                    }
                }
                // break not needed because every if clause contains return!
                // no break
            case 7:
                if ('[/' === $needle) {
                    // this was case insensitive match
                    if (strtolower(substr($this->_text, $this->_cpos + strlen($needle), strlen($this->_topNode('name')) + 1)) === strtolower($this->_topNode('name').']')) {
                        // this matched
                        $this->_cpos += strlen($this->_topNode('name')) + 1;
                    } else {
                        // it didn't match
                        $this->_appendText($needle);
                        return true;
                    }
                }
                $closecount = $this->_savedCloseCount;
                if (!$this->_topNode('validate')) {
                    return $this->_reparseAfterCurrentBlock();
                }
                // do we have to close subnodes?
                if ($closecount) {
                    // get top node
                    $mynode = $this->_stack[count($this->_stack) - 1];
                    // close necessary nodes
                    for ($i = 0; $i <= $closecount; ++$i) {
                        if (!$this->_popNode()) {
                            return false;
                        }
                    }
                    if (!$this->_pushNode($mynode)) {
                        return false;
                    }
                }
                $this->_setStatus(0);
                $this->_popNode();
                return true;
            default:
                return false;
        }
        return true;
    }

    /**
     * Open the next element.
     *
     * @return bool
     */
    public function _openElement($type = 0)
    {
        $name = $this->_getCanonicalName($this->_topNode('name'));
        if (false === $name) {
            return $this->_reparseAfterCurrentBlock();
        }
        $occ_type = $this->getCodeFlag($name, 'occurrence_type', 'string');
        if (null !== $occ_type && isset($this->_maxOccurrences[$occ_type])) {
            $max_occs = $this->_maxOccurrences[$occ_type];
            $occs = $this->_root->getNodeCountByCriterium('flag:occurrence_type', $occ_type);
            if ($occs >= $max_occs) {
                return $this->_reparseAfterCurrentBlock();
            }
        }
        $closecount = 0;
        $this->_topNode('setCodeInfo', $this->_codes[$name]);
        if (!$this->_isOpenable($name, $closecount)) {
            return $this->_reparseAfterCurrentBlock();
        }
        $this->_setStatus(0);
        switch ($type) {
        case 0:
            $cond = $this->_isUseContent($this->_stack[count($this->_stack) - 1], false);
            break;
        case 1:
            $cond = $this->_isUseContent($this->_stack[count($this->_stack) - 1], true);
            break;
        case 2:
            $cond = $this->_isUseContent($this->_stack[count($this->_stack) - 1], true);
            break;
        default:
            $cond = false;
            break;
        }
        if ($cond) {
            $this->_savedCloseCount = $closecount;
            $this->_setStatus(7);
            return true;
        }
        if (!$this->_topNode('validate')) {
            return $this->_reparseAfterCurrentBlock();
        }
        // do we have to close subnodes?
        if ($closecount) {
            // get top node
            $mynode = $this->_stack[count($this->_stack) - 1];
            // close necessary nodes
            for ($i = 0; $i <= $closecount; ++$i) {
                if (!$this->_popNode()) {
                    return false;
                }
            }
            if (!$this->_pushNode($mynode)) {
                return false;
            }
        }

        if ('simple_replace_single' === $this->_codes[$name]['callback_type'] || 'callback_replace_single' === $this->_codes[$name]['callback_type']) {
            if (!$this->_popNode()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Is a node closeable?
     *
     * @return bool
     */
    public function _isCloseable($name, &$closecount)
    {
        $node = $this->_findNamedNode($name, false);
        if (false === $node) {
            return false;
        }
        $scount = count($this->_stack);
        for ($i = $scount - 1; $i > 0; --$i) {
            ++$closecount;
            if ($this->_stack[$i]->equals($node)) {
                return true;
            }
            if (BBCODE_CLOSETAG_MUSTEXIST === $this->_stack[$i]->getFlag('closetag', 'integer', BBCODE_CLOSETAG_IMPLICIT)) {
                return false;
            }
        }
        return false;
    }

    /**
     * Revalidate codes when close tags appear.
     *
     * @return bool
     */
    public function _validateCloseTags($closecount)
    {
        $scount = count($this->_stack);
        for ($i = $scount - 1; $i >= $scount - $closecount; --$i) {
            if ($this->_validateAgain) {
                if (!$this->_stack[$i]->validate('validate_again')) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Is a node openable?
     *
     * @return bool
     */
    public function _isOpenable($name, &$closecount)
    {
        if (!isset($this->_codes[$name])) {
            return false;
        }

        $closecount = 0;

        $allowed_within = $this->_codes[$name]['allowed_within'];
        $not_allowed_within = $this->_codes[$name]['not_allowed_within'];

        $scount = count($this->_stack);
        if (2 === $scount) { // top level element
            if (!in_array($this->_rootContentType, $allowed_within)) {
                return false;
            }
        } else {
            if (!in_array($this->_stack[$scount - 2]->_codeInfo['content_type'], $allowed_within)) {
                return $this->_isOpenableWithClose($name, $closecount);
            }
        }

        for ($i = 1; $i < $scount - 1; ++$i) {
            if (in_array($this->_stack[$i]->_codeInfo['content_type'], $not_allowed_within)) {
                return $this->_isOpenableWithClose($name, $closecount);
            }
        }

        return true;
    }

    /**
     * Is a node openable by closing other nodes?
     *
     * @return bool
     */
    public function _isOpenableWithClose($name, &$closecount)
    {
        $tnname = $this->_getCanonicalName($this->_topNode('name'));
        if (!in_array($this->getCodeFlag($tnname, 'closetag', 'integer', BBCODE_CLOSETAG_IMPLICIT), [BBCODE_CLOSETAG_FORBIDDEN, BBCODE_CLOSETAG_OPTIONAL])) {
            return false;
        }
        $node = $this->_findNamedNode($name, true);
        if (false === $node) {
            return false;
        }
        $scount = count($this->_stack);
        if ($scount < 3) {
            return false;
        }
        for ($i = $scount - 2; $i > 0; --$i) {
            ++$closecount;
            if ($this->_stack[$i]->equals($node)) {
                return true;
            }
            if (in_array($this->_stack[$i]->getFlag('closetag', 'integer', BBCODE_CLOSETAG_IMPLICIT), [BBCODE_CLOSETAG_IMPLICIT_ON_CLOSE_ONLY, BBCODE_CLOSETAG_MUSTEXIST])) {
                return false;
            }
            if ($this->_validateAgain) {
                if (!$this->_stack[$i]->validate('validate_again')) {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Abstract method: Close remaining blocks.
     */
    public function _closeRemainingBlocks()
    {
        // everything closed
        if (1 === count($this->_stack)) {
            return true;
        }
        // not everything close
        if ($this->strict) {
            return false;
        }
        while (count($this->_stack) > 1) {
            if (BBCODE_CLOSETAG_MUSTEXIST === $this->_topNode('getFlag', 'closetag', 'integer', BBCODE_CLOSETAG_IMPLICIT)) {
                return false; // sorry
            }
            $res = $this->_popNode();
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    /**
     * Find a node with a specific name in stack.
     *
     * @return mixed
     */
    public function &_findNamedNode($name, $searchdeeper = false)
    {
        $lname = $this->_getCanonicalName($name);
        $case_sensitive = $this->_caseSensitive && $this->getCodeFlag($lname, 'case_sensitive', 'boolean', true);
        if ($case_sensitive) {
            $name = strtolower($name);
        }
        $scount = count($this->_stack);
        if ($searchdeeper) {
            --$scount;
        }
        for ($i = $scount - 1; $i > 0; --$i) {
            if (!$case_sensitive) {
                $cmp_name = strtolower($this->_stack[$i]->name());
            } else {
                $cmp_name = $this->_stack[$i]->name();
            }
            if ($cmp_name === $lname) {
                return $this->_stack[$i];
            }
        }
        $result = false;
        return $result;
    }

    /**
     * Abstract method: Output tree.
     *
     * @return bool
     */
    public function _outputTree()
    {
        if ($this->_noOutput) {
            return true;
        }
        $output = $this->_outputNode($this->_root);
        if (is_string($output)) {
            $this->_output = $this->_applyPostfilters($output);
            unset($output);
            return true;
        }

        return false;
    }

    /**
     * Output a node.
     *
     * @return bool
     */
    public function _outputNode(&$node)
    {
        $output = '';
        if (STRINGPARSER_BBCODE_NODE_PARAGRAPH === $node->_type || STRINGPARSER_BBCODE_NODE_ELEMENT === $node->_type || STRINGPARSER_NODE_ROOT === $node->_type) {
            $ccount = count($node->_children);
            for ($i = 0; $i < $ccount; ++$i) {
                $suboutput = $this->_outputNode($node->_children[$i]);
                if (!is_string($suboutput)) {
                    return false;
                }
                $output .= $suboutput;
            }
            if (STRINGPARSER_BBCODE_NODE_PARAGRAPH === $node->_type) {
                return $this->_paragraphHandling['start_tag'].$output.$this->_paragraphHandling['end_tag'];
            }
            if (STRINGPARSER_BBCODE_NODE_ELEMENT === $node->_type) {
                return $node->getReplacement($output);
            }
            return $output;
        } elseif (STRINGPARSER_NODE_TEXT === $node->_type) {
            $output = $node->content;
            $before = '';
            $after = '';
            $ol = strlen($output);
            switch ($node->getFlag('newlinemode.begin', 'integer', BBCODE_NEWLINE_PARSE)) {
            case BBCODE_NEWLINE_IGNORE:
                if ($ol && "\n" === $output[0]) {
                    $before = "\n";
                }
                // don't break!
                // no break
            case BBCODE_NEWLINE_DROP:
                if ($ol && "\n" === $output[0]) {
                    $output = substr($output, 1);
                    --$ol;
                }
                break;
            }
            switch ($node->getFlag('newlinemode.end', 'integer', BBCODE_NEWLINE_PARSE)) {
            case BBCODE_NEWLINE_IGNORE:
                if ($ol && "\n" === $output[$ol - 1]) {
                    $after = "\n";
                }
                // don't break!
                // no break
            case BBCODE_NEWLINE_DROP:
                if ($ol && "\n" === $output[$ol - 1]) {
                    $output = substr($output, 0, -1);
                    --$ol;
                }
                break;
            }
            // can't do anything
            if (null === $node->_parent) {
                return $before.$output.$after;
            }
            if (STRINGPARSER_BBCODE_NODE_PARAGRAPH === $node->_parent->_type) {
                $parent = $node->_parent;
                unset($node);
                $node = $parent;
                unset($parent);
                // if no parent for this paragraph
                if (null === $node->_parent) {
                    return $before.$output.$after;
                }
            }
            if (STRINGPARSER_NODE_ROOT === $node->_parent->_type) {
                return $before.$this->_applyParsers($this->_rootContentType, $output).$after;
            }
            if (STRINGPARSER_BBCODE_NODE_ELEMENT === $node->_parent->_type) {
                return $before.$this->_applyParsers($node->_parent->_codeInfo['content_type'], $output).$after;
            }
            return $before.$output.$after;
        }
    }

    /**
     * Abstract method: Manipulate the tree.
     *
     * @return bool
     */
    public function _modifyTree()
    {
        // first pass: try to do newline handling
        $nodes = $this->_root->getNodesByCriterium('needsTextNodeModification', true);
        $nodes_count = count($nodes);
        for ($i = 0; $i < $nodes_count; ++$i) {
            $v = $nodes[$i]->getFlag('opentag.before.newline', 'integer', BBCODE_NEWLINE_PARSE);
            if (BBCODE_NEWLINE_PARSE !== $v) {
                $n = $nodes[$i]->findPrevAdjentTextNode();
                if (!is_null($n)) {
                    $n->setFlag('newlinemode.end', $v);
                }
                unset($n);
            }
            $v = $nodes[$i]->getFlag('opentag.after.newline', 'integer', BBCODE_NEWLINE_PARSE);
            if (BBCODE_NEWLINE_PARSE !== $v) {
                $n = $nodes[$i]->firstChildIfText();
                if (!is_null($n)) {
                    $n->setFlag('newlinemode.begin', $v);
                }
                unset($n);
            }
            $v = $nodes[$i]->getFlag('closetag.before.newline', 'integer', BBCODE_NEWLINE_PARSE);
            if (BBCODE_NEWLINE_PARSE !== $v) {
                $n = $nodes[$i]->lastChildIfText();
                if (!is_null($n)) {
                    $n->setFlag('newlinemode.end', $v);
                }
                unset($n);
            }
            $v = $nodes[$i]->getFlag('closetag.after.newline', 'integer', BBCODE_NEWLINE_PARSE);
            if (BBCODE_NEWLINE_PARSE !== $v) {
                $n = $nodes[$i]->findNextAdjentTextNode();
                if (!is_null($n)) {
                    $n->setFlag('newlinemode.begin', $v);
                }
                unset($n);
            }
        }

        // second pass a: do paragraph handling on root element
        if ($this->_rootParagraphHandling) {
            $res = $this->_handleParagraphs($this->_root);
            if (!$res) {
                return false;
            }
        }

        // second pass b: do paragraph handling on other elements
        unset($nodes);
        $nodes = $this->_root->getNodesByCriterium('flag:paragraphs', true);
        $nodes_count = count($nodes);
        for ($i = 0; $i < $nodes_count; ++$i) {
            $res = $this->_handleParagraphs($nodes[$i]);
            if (!$res) {
                return false;
            }
        }

        // second pass c: search for empty paragraph nodes and remove them
        unset($nodes);
        $nodes = $this->_root->getNodesByCriterium('empty', true);
        $nodes_count = count($nodes);
        if (isset($parent)) {
            unset($parent);
            $parent = null;
        }
        for ($i = 0; $i < $nodes_count; ++$i) {
            if (STRINGPARSER_BBCODE_NODE_PARAGRAPH !== $nodes[$i]->_type) {
                continue;
            }
            unset($parent);
            $parent = $nodes[$i]->_parent;
            $parent->removeChild($nodes[$i], true);
        }

        return true;
    }

    /**
     * Handle paragraphs.
     *
     * @param object $node The node to handle
     *
     * @return bool
     */
    public function _handleParagraphs(&$node)
    {
        // if this node is already a subnode of a paragraph node, do NOT
        // do paragraph handling on this node!
        if ($this->_hasParagraphAncestor($node)) {
            return true;
        }
        $dest_nodes = [];
        $last_node_was_paragraph = false;
        $prevtype = STRINGPARSER_NODE_TEXT;
        $paragraph = null;
        while (count($node->_children)) {
            $mynode = $node->_children[0];
            $node->removeChild($mynode);
            $subprevtype = $prevtype;
            $sub_nodes = $this->_breakupNodeByParagraphs($mynode);
            for ($i = 0; $i < count($sub_nodes); ++$i) {
                if (!$last_node_was_paragraph || ($prevtype === $sub_nodes[$i]->_type && (0 !== (int)$i || STRINGPARSER_BBCODE_NODE_ELEMENT !== $prevtype))) {
                    unset($paragraph);
                    $paragraph = new StringParser_BBCode_Node_Paragraph();
                }
                $prevtype = $sub_nodes[$i]->_type;
                if (STRINGPARSER_BBCODE_NODE_ELEMENT !== $sub_nodes[$i]->_type || BBCODE_PARAGRAPH_BLOCK_ELEMENT !== $sub_nodes[$i]->getFlag('paragraph_type', 'integer', BBCODE_PARAGRAPH_ALLOW_BREAKUP)) {
                    $paragraph->appendChild($sub_nodes[$i]);
                    $dest_nodes[] = $paragraph;
                    $last_node_was_paragraph = true;
                } else {
                    $dest_nodes[] = $sub_nodes[$i];
                    $last_onde_was_paragraph = false;
                    unset($paragraph);
                    $paragraph = new StringParser_BBCode_Node_Paragraph();
                }
            }
        }
        $count = count($dest_nodes);
        for ($i = 0; $i < $count; ++$i) {
            $node->appendChild($dest_nodes[$i]);
        }
        unset($dest_nodes);
        unset($paragraph);
        return true;
    }

    /**
     * Search for a paragraph node in tree in upward direction.
     *
     * @param object $node The node to analyze
     *
     * @return bool
     */
    public function _hasParagraphAncestor(&$node)
    {
        if (null === $node->_parent) {
            return false;
        }
        $parent = $node->_parent;
        if (STRINGPARSER_BBCODE_NODE_PARAGRAPH === $parent->_type) {
            return true;
        }
        return $this->_hasParagraphAncestor($parent);
    }

    /**
     * Break up nodes.
     *
     * @param object $node The node to break up
     *
     * @return array
     */
    public function &_breakupNodeByParagraphs(&$node)
    {
        $detect_string = $this->_paragraphHandling['detect_string'];
        $dest_nodes = [];
        // text node => no problem
        if (STRINGPARSER_NODE_TEXT === $node->_type) {
            $cpos = 0;
            while (false !== ($npos = strpos($node->content, $detect_string, $cpos))) {
                $subnode = new StringParser_Node_Text(substr($node->content, $cpos, $npos - $cpos), $node->occurredAt + $cpos);
                // copy flags
                foreach ($node->_flags as $flag => $value) {
                    if ('newlinemode.begin' === $flag) {
                        if (0 === $cpos) {
                            $subnode->setFlag($flag, $value);
                        }
                    } elseif ('newlinemode.end' === $flag) {
                        // do nothing
                    } else {
                        $subnode->setFlag($flag, $value);
                    }
                }
                $dest_nodes[] = $subnode;
                unset($subnode);
                $cpos = $npos + strlen($detect_string);
            }
            $subnode = new StringParser_Node_Text(substr($node->content, $cpos), $node->occurredAt + $cpos);
            if (0 === $cpos) {
                $value = $node->getFlag('newlinemode.begin', 'integer', null);
                if (null !== $value) {
                    $subnode->setFlag('newlinemode.begin', $value);
                }
            }
            $value = $node->getFlag('newlinemode.end', 'integer', null);
            if (null !== $value) {
                $subnode->setFlag('newlinemode.end', $value);
            }
            $dest_nodes[] = $subnode;
            unset($subnode);
            return $dest_nodes;
        }
        // not a text node or an element node => no way
        if (STRINGPARSER_BBCODE_NODE_ELEMENT !== $node->_type) {
            $dest_nodes[] = $node;
            return $dest_nodes;
        }
        if (BBCODE_PARAGRAPH_ALLOW_BREAKUP !== $node->getFlag('paragraph_type', 'integer', BBCODE_PARAGRAPH_ALLOW_BREAKUP) || !count($node->_children)) {
            $dest_nodes[] = $node;
            return $dest_nodes;
        }
        $dest_node = $node->duplicate();
        $nodecount = count($node->_children);
        // now this node allows breakup - do it
        for ($i = 0; $i < $nodecount; ++$i) {
            $firstnode = $node->_children[0];
            $node->removeChild($firstnode);
            $sub_nodes = $this->_breakupNodeByParagraphs($firstnode);
            for ($j = 0; $j < count($sub_nodes); ++$j) {
                if (0 !== (int)$j) {
                    $dest_nodes[] = $dest_node;
                    unset($dest_node);
                    $dest_node = $node->duplicate();
                }
                $dest_node->appendChild($sub_nodes[$j]);
            }
            unset($sub_nodes);
        }
        $dest_nodes[] = $dest_node;
        return $dest_nodes;
    }

    /**
     * Is this node a usecontent node.
     *
     * @param object $node        The node to check
     * @param bool   $check_attrs Also check whether 'usecontent?'-attributes exist
     *
     * @return bool
     */
    public function _isUseContent(&$node, $check_attrs = false)
    {
        $name = $this->_getCanonicalName($node->name());
        // this should NOT happen
        if (false === $name) {
            return false;
        }
        if ('usecontent' === $this->_codes[$name]['callback_type']) {
            return true;
        }
        $result = false;
        if ('callback_replace?' === $this->_codes[$name]['callback_type']) {
            $result = true;
        } elseif ('usecontent?' !== $this->_codes[$name]['callback_type']) {
            return false;
        }
        if (false === $check_attrs) {
            return !$result;
        }
        $attributes = array_keys($this->_topNodeVar('_attributes'));
        $p = @$this->_codes[$name]['callback_params']['usecontent_param'];
        if (is_array($p)) {
            foreach ($p as $param) {
                if (in_array($param, $attributes)) {
                    return $result;
                }
            }
        } else {
            if (in_array($p, $attributes)) {
                return $result;
            }
        }
        return !$result;
    }

    /**
     * Get canonical name of a code.
     *
     * @param string $name
     *
     * @return string
     */
    public function _getCanonicalName($name)
    {
        if (isset($this->_codes[$name])) {
            return $name;
        }
        $found = false;
        // try to find the code in the code list
        foreach (array_keys($this->_codes) as $rname) {
            // match
            if (strtolower($rname) === strtolower($name)) {
                $found = $rname;
                break;
            }
        }
        if (false === $found || ($this->_caseSensitive && $this->getCodeFlag($found, 'case_sensitive', 'boolean', true))) {
            return false;
        }
        return $rname;
    }
}

/*
 * Node type: BBCode Element node
 * @see StringParser_BBCode_Node_Element::_type
 */
define('STRINGPARSER_BBCODE_NODE_ELEMENT', 32);

/*
 * Node type: BBCode Paragraph node
 * @see StringParser_BBCode_Node_Paragraph::_type
 */
define('STRINGPARSER_BBCODE_NODE_PARAGRAPH', 33);

/**
 * BBCode String parser paragraph node class.
 */
class StringParser_BBCode_Node_Paragraph extends StringParser_Node
{
    /**
     * The type of this node.
     *
     * This node is a bbcode paragraph node.
     *
     * @var int
     *
     * @see STRINGPARSER_BBCODE_NODE_PARAGRAPH
     */
    public $_type = STRINGPARSER_BBCODE_NODE_PARAGRAPH;

    /**
     * Determines whether a criterium matches this node.
     *
     * @param string $criterium The criterium that is to be checked
     * @param mixed  $value     The value that is to be compared
     *
     * @return bool True if this node matches that criterium
     */
    public function matchesCriterium($criterium, $value)
    {
        if ('empty' === $criterium) {
            if (!count($this->_children)) {
                return true;
            }
            if (count($this->_children) > 1) {
                return false;
            }
            if (STRINGPARSER_NODE_TEXT !== $this->_children[0]->_type) {
                return false;
            }
            if (!strlen($this->_children[0]->content)) {
                return true;
            }
            if (strlen($this->_children[0]->content) > 2) {
                return false;
            }
            $f_begin = $this->_children[0]->getFlag('newlinemode.begin', 'integer', BBCODE_NEWLINE_PARSE);
            $f_end = $this->_children[0]->getFlag('newlinemode.end', 'integer', BBCODE_NEWLINE_PARSE);
            $content = $this->_children[0]->content;
            if (BBCODE_NEWLINE_PARSE !== $f_begin && "\n" === $content[0]) {
                $content = substr($content, 1);
            }
            if (BBCODE_NEWLINE_PARSE !== $f_end && "\n" === $content[strlen($content) - 1]) {
                $content = substr($content, 0, -1);
            }
            if (!strlen($content)) {
                return true;
            }
            return false;
        }
    }
}

/**
 * BBCode String parser element node class.
 */
class StringParser_BBCode_Node_Element extends StringParser_Node
{
    /**
     * The type of this node.
     *
     * This node is a bbcode element node.
     *
     * @var int
     *
     * @see STRINGPARSER_BBCODE_NODE_ELEMENT
     */
    public $_type = STRINGPARSER_BBCODE_NODE_ELEMENT;

    /**
     * Element name.
     *
     * @var string
     *
     * @see StringParser_BBCode_Node_Element::name
     * @see StringParser_BBCode_Node_Element::setName
     * @see StringParser_BBCode_Node_Element::appendToName
     */
    public $_name = '';

    /**
     * Element flags.
     *
     * @var array
     */
    public $_flags = [];

    /**
     * Element attributes.
     *
     * @var array
     */
    public $_attributes = [];

    /**
     * Had a close tag.
     *
     * @var bool
     */
    public $_hadCloseTag = false;

    /**
     * Was processed by paragraph handling.
     *
     * @var bool
     */
    public $_paragraphHandled = false;

    //////////////////////////////////////////////////

    /**
     * Duplicate this node (but without children / parents).
     *
     * @return object
     */
    public function &duplicate()
    {
        $newnode = new StringParser_BBCode_Node_Element($this->occurredAt);
        $newnode->_name = $this->_name;
        $newnode->_flags = $this->_flags;
        $newnode->_attributes = $this->_attributes;
        $newnode->_hadCloseTag = $this->_hadCloseTag;
        $newnode->_paragraphHandled = $this->_paragraphHandled;
        $newnode->_codeInfo = $this->_codeInfo;
        return $newnode;
    }

    /**
     * Retreive name of this element.
     *
     * @return string
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * Set name of this element.
     *
     * @param string $name The new name of the element
     */
    public function setName($name)
    {
        $this->_name = $name;
        return true;
    }

    /**
     * Append to name of this element.
     *
     * @param string $chars The chars to append to the name of the element
     */
    public function appendToName($chars)
    {
        $this->_name .= $chars;
        return true;
    }

    /**
     * Append to attribute of this element.
     *
     * @param string $name  The name of the attribute
     * @param string $chars The chars to append to the attribute of the element
     */
    public function appendToAttribute($name, $chars)
    {
        if (!isset($this->_attributes[$name])) {
            $this->_attributes[$name] = $chars;
            return true;
        }
        $this->_attributes[$name] .= $chars;
        return true;
    }

    /**
     * Set attribute.
     *
     * @param string $name  The name of the attribute
     * @param string $value The new value of the attribute
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return true;
    }

    /**
     * Set code info.
     *
     * @param array $info The code info array
     */
    public function setCodeInfo($info)
    {
        $this->_codeInfo = $info;
        $this->_flags = $info['flags'];
        return true;
    }

    /**
     * Get attribute value.
     *
     * @param string $name The name of the attribute
     */
    public function attribute($name)
    {
        if (!isset($this->_attributes[$name])) {
            return null;
        }
        return $this->_attributes[$name];
    }

    /**
     * Set flag that this element had a close tag.
     */
    public function setHadCloseTag()
    {
        $this->_hadCloseTag = true;
    }

    /**
     * Set flag that this element was already processed by paragraph handling.
     */
    public function setParagraphHandled()
    {
        $this->_paragraphHandled = true;
    }

    /**
     * Get flag if this element was already processed by paragraph handling.
     *
     * @return bool
     */
    public function paragraphHandled()
    {
        return $this->_paragraphHandled;
    }

    /**
     * Get flag if this element had a close tag.
     *
     * @return bool
     */
    public function hadCloseTag()
    {
        return $this->_hadCloseTag;
    }

    /**
     * Determines whether a criterium matches this node.
     *
     * @param string $criterium The criterium that is to be checked
     * @param mixed  $value     The value that is to be compared
     *
     * @return bool True if this node matches that criterium
     */
    public function matchesCriterium($criterium, $value)
    {
        if ('tagName' === $criterium) {
            return $value === $this->_name;
        }
        if ('needsTextNodeModification' === $criterium) {
            return (BBCODE_NEWLINE_PARSE !== $this->getFlag('opentag.before.newline', 'integer', BBCODE_NEWLINE_PARSE) || BBCODE_NEWLINE_PARSE !== $this->getFlag('opentag.after.newline', 'integer', BBCODE_NEWLINE_PARSE) || ($this->_hadCloseTag && (BBCODE_NEWLINE_PARSE !== $this->getFlag('closetag.before.newline', 'integer', BBCODE_NEWLINE_PARSE) || BBCODE_NEWLINE_PARSE !== $this->getFlag('closetag.after.newline', 'integer', BBCODE_NEWLINE_PARSE)))) === (bool) $value;
        }
        if ('flag:' === substr($criterium, 0, 5)) {
            $criterium = substr($criterium, 5);
            return $this->getFlag($criterium) === $value;
        }
        if ('!flag:' === substr($criterium, 0, 6)) {
            $criterium = substr($criterium, 6);
            return $this->getFlag($criterium) !== $value;
        }
        if ('flag=:' === substr($criterium, 0, 6)) {
            $criterium = substr($criterium, 6);
            return $this->getFlag($criterium) === $value;
        }
        if ('!flag=:' === substr($criterium, 0, 7)) {
            $criterium = substr($criterium, 7);
            return $this->getFlag($criterium) !== $value;
        }
        return parent::matchesCriterium($criterium, $value);
    }

    /**
     * Get first child if it is a text node.
     *
     * @return mixed
     */
    public function &firstChildIfText()
    {
        $ret = $this->firstChild();
        if (is_null($ret)) {
            return $ret;
        }
        if (STRINGPARSER_NODE_TEXT !== $ret->_type) {
            // DON'T DO $ret = null WITHOUT unset BEFORE!
            // ELSE WE WILL ERASE THE NODE ITSELF! EVIL!
            unset($ret);
            $ret = null;
        }
        return $ret;
    }

    /**
     * Get last child if it is a text node AND if this element had a close tag.
     *
     * @return mixed
     */
    public function &lastChildIfText()
    {
        $ret = $this->lastChild();
        if (is_null($ret)) {
            return $ret;
        }
        if (STRINGPARSER_NODE_TEXT !== $ret->_type || !$this->_hadCloseTag) {
            // DON'T DO $ret = null WITHOUT unset BEFORE!
            // ELSE WE WILL ERASE THE NODE ITSELF! EVIL!
            if (STRINGPARSER_NODE_TEXT !== $ret->_type && !$ret->hadCloseTag()) {
                $ret2 = $ret->_findPrevAdjentTextNodeHelper();
                unset($ret);
                $ret = $ret2;
                unset($ret2);
            } else {
                unset($ret);
                $ret = null;
            }
        }
        return $ret;
    }

    /**
     * Find next adjent text node after close tag.
     *
     * returns the node or null if none exists
     *
     * @return mixed
     */
    public function &findNextAdjentTextNode()
    {
        $ret = null;
        if (is_null($this->_parent)) {
            return $ret;
        }
        if (!$this->_hadCloseTag) {
            return $ret;
        }
        $ccount = count($this->_parent->_children);
        $found = false;
        for ($i = 0; $i < $ccount; ++$i) {
            if ($this->_parent->_children[$i]->equals($this)) {
                $found = $i;
                break;
            }
        }
        if (false === $found) {
            return $ret;
        }
        if ($found < $ccount - 1) {
            if (STRINGPARSER_NODE_TEXT === $this->_parent->_children[$found + 1]->_type) {
                return $this->_parent->_children[$found + 1];
            }
            return $ret;
        }
        if (STRINGPARSER_BBCODE_NODE_ELEMENT === $this->_parent->_type && !$this->_parent->hadCloseTag()) {
            $ret = $this->_parent->findNextAdjentTextNode();
            return $ret;
        }
        return $ret;
    }

    /**
     * Find previous adjent text node before open tag.
     *
     * returns the node or null if none exists
     *
     * @return mixed
     */
    public function &findPrevAdjentTextNode()
    {
        $ret = null;
        if (is_null($this->_parent)) {
            return $ret;
        }
        $ccount = count($this->_parent->_children);
        $found = false;
        for ($i = 0; $i < $ccount; ++$i) {
            if ($this->_parent->_children[$i]->equals($this)) {
                $found = $i;
                break;
            }
        }
        if (false === $found) {
            return $ret;
        }
        if ($found > 0) {
            if (STRINGPARSER_NODE_TEXT === $this->_parent->_children[$found - 1]->_type) {
                return $this->_parent->_children[$found - 1];
            }
            if (!$this->_parent->_children[$found - 1]->hadCloseTag()) {
                $ret = $this->_parent->_children[$found - 1]->_findPrevAdjentTextNodeHelper();
            }
            return $ret;
        }
        return $ret;
    }

    /**
     * Helper function for findPrevAdjentTextNode.
     *
     * Looks at the last child node; if it's a text node, it returns it,
     * if the element node did not have an open tag, it calls itself
     * recursively.
     */
    public function &_findPrevAdjentTextNodeHelper()
    {
        $lastnode = $this->lastChild();
        if (null === $lastnode || STRINGPARSER_NODE_TEXT === $lastnode->_type) {
            return $lastnode;
        }
        if (!$lastnode->hadCloseTag()) {
            $ret = $lastnode->_findPrevAdjentTextNodeHelper();
        } else {
            $ret = null;
        }
        return $ret;
    }

    /**
     * Get Flag.
     *
     * @param string $flag    The requested flag
     * @param string $type    The requested type of the return value
     * @param mixed  $default The default return value
     *
     * @return mixed
     */
    public function getFlag($flag, $type = 'mixed', $default = null)
    {
        if (!isset($this->_flags[$flag])) {
            return $default;
        }
        $return = $this->_flags[$flag];
        if ('mixed' !== $type) {
            settype($return, $type);
        }
        return $return;
    }

    /**
     * Set a flag.
     *
     * @param string $name  The name of the flag
     * @param mixed  $value The value of the flag
     */
    public function setFlag($name, $value)
    {
        $this->_flags[$name] = $value;
        return true;
    }

    /**
     * Validate code.
     *
     * @param string $action The action which is to be called ('validate'
     *                       for first validation, 'validate_again' for
     *                       second validation (optional))
     *
     * @return bool
     */
    public function validate($action = 'validate')
    {
        if ('validate' !== $action && 'validate_again' !== $action) {
            return false;
        }
        if ('simple_replace' !== $this->_codeInfo['callback_type'] && 'simple_replace_single' !== $this->_codeInfo['callback_type']) {
            if (!is_callable($this->_codeInfo['callback_func'])) {
                return false;
            }

            if (('usecontent' === $this->_codeInfo['callback_type'] || 'usecontent?' === $this->_codeInfo['callback_type'] || 'callback_replace?' === $this->_codeInfo['callback_type']) && 1 === count($this->_children) && STRINGPARSER_NODE_TEXT === $this->_children[0]->_type) {
                // we have to make sure the object gets passed on as a reference
                // if we do call_user_func(..., &$this) this will clash with PHP5
                $callArray = [$action, $this->_attributes, $this->_children[0]->content, $this->_codeInfo['callback_params']];
                $callArray[] = $this;
                $res = call_user_func_array($this->_codeInfo['callback_func'], $callArray);
                if ($res) {
                    // ok, now, if we've got a usecontent type, set a flag that
                    // this may not be broken up by paragraph handling!
                    // but PLEASE do NOT change if already set to any other setting
                    // than BBCODE_PARAGRAPH_ALLOW_BREAKUP because we could
                    // override e.g. BBCODE_PARAGRAPH_BLOCK_ELEMENT!
                    $val = $this->getFlag('paragraph_type', 'integer', BBCODE_PARAGRAPH_ALLOW_BREAKUP);
                    if (BBCODE_PARAGRAPH_ALLOW_BREAKUP === $val) {
                        $this->_flags['paragraph_type'] = BBCODE_PARAGRAPH_ALLOW_INSIDE;
                    }
                }
                return $res;
            }

            // we have to make sure the object gets passed on as a reference
            // if we do call_user_func(..., &$this) this will clash with PHP5
            $callArray = [$action, $this->_attributes, null, $this->_codeInfo['callback_params']];
            $callArray[] = $this;
            return call_user_func_array($this->_codeInfo['callback_func'], $callArray);
        }
        return (bool) (!count($this->_attributes));
    }

    /**
     * Get replacement for this code.
     *
     * @param string $subcontent The content of all sub-nodes
     *
     * @return string
     */
    public function getReplacement($subcontent)
    {
        if ('simple_replace' === $this->_codeInfo['callback_type'] || 'simple_replace_single' === $this->_codeInfo['callback_type']) {
            if ('simple_replace_single' === $this->_codeInfo['callback_type']) {
                if (strlen($subcontent)) { // can't be!
                    return false;
                }
                return $this->_codeInfo['callback_params']['start_tag'];
            }
            return $this->_codeInfo['callback_params']['start_tag'].$subcontent.$this->_codeInfo['callback_params']['end_tag'];
        }
        // else usecontent, usecontent? or callback_replace or callback_replace_single
        // => call function (the function is callable, determined in validate()!)

        // we have to make sure the object gets passed on as a reference
        // if we do call_user_func(..., &$this) this will clash with PHP5
        $callArray = ['output', $this->_attributes, $subcontent, $this->_codeInfo['callback_params']];
        $callArray[] = $this;
        return call_user_func_array($this->_codeInfo['callback_func'], $callArray);
    }

    /**
     * Dump this node to a string.
     *
     * @return string
     */
    public function _dumpToString()
    {
        $str = 'bbcode "'.substr(preg_replace('/\s+/', ' ', $this->_name), 0, 40).'"';
        if (count($this->_attributes)) {
            $attribs = array_keys($this->_attributes);
            sort($attribs);
            $str .= ' (';
            $i = 0;
            foreach ($attribs as $attrib) {
                if (0 !== (int)$i) {
                    $str .= ', ';
                }
                $str .= $attrib.'="';
                $str .= substr(preg_replace('/\s+/', ' ', $this->_attributes[$attrib]), 0, 10);
                $str .= '"';
                ++$i;
            }
            $str .= ')';
        }
        return $str;
    }
}
