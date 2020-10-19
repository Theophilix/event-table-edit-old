<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/*
 * String parser mode: Search for the next character
 * @see StringParser::_parserMode
 */
define('STRINGPARSER_MODE_SEARCH', 1);
/*
 * String parser mode: Look at each character of the string
 * @see StringParser::_parserMode
 */
define('STRINGPARSER_MODE_LOOP', 2);
/*
 * Filter type: Prefilter
 * @see StringParser::addFilter, StringParser::_prefilters
 */
define('STRINGPARSER_FILTER_PRE', 1);
/*
 * Filter type: Postfilter
 * @see StringParser::addFilter, StringParser::_postfilters
 */
define('STRINGPARSER_FILTER_POST', 2);

/**
 * Generic string parser class.
 *
 * This is an abstract class for any type of string parser.
 */
class StringParser
{
    /**
     * String parser mode.
     *
     * There are two possible modes: searchmode and loop mode. In loop mode
     * every single character is looked at in a loop and it is then decided
     * what action to take. This is the most straight-forward approach to
     * string parsing but due to the nature of PHP as a scripting language,
     * it can also cost performance. In search mode the class posseses a
     * list of relevant characters for parsing and uses the
     * {@link PHP_MANUAL#strpos strpos} function to search for the next
     * relevant character. The search mode will be faster than the loop mode
     * in most circumstances but it is also more difficult to implement.
     * The subclass that does the string parsing itself will define which
     * mode it will implement.
     *
     * @var int
     *
     * @see STRINGPARSER_MODE_SEARCH, STRINGPARSER_MODE_LOOP
     */
    public $_parserMode = STRINGPARSER_MODE_SEARCH;

    /**
     * Raw text.
     *
     * @var string
     */
    public $_text = '';

    /**
     * Parse stack.
     *
     * @var array
     */
    public $_stack = [];

    /**
     * Current position in raw text.
     *
     * @var int
     */
    public $_cpos = -1;

    /**
     * Root node.
     *
     * @var mixed
     */
    public $_root = null;

    /**
     * Length of the text.
     *
     * @var int
     */
    public $_length = -1;

    /**
     * Flag if this object is already parsing a text.
     *
     * This flag is to prevent recursive calls to the parse() function that
     * would cause very nasty things.
     *
     * @var bool
     */
    public $_parsing = false;

    /**
     * Strict mode.
     *
     * Whether to stop parsing if a parse error occurs.
     *
     * @var bool
     */
    public $strict = false;

    /**
     * Characters or strings to look for.
     *
     * @var array
     */
    public $_charactersSearch = [];

    /**
     * Characters currently allowed.
     *
     * Note that this will only be evaluated in loop mode; in search mode
     * this would ruin every performance increase. Note that only single
     * characters are permitted here, no strings. Please also note that in
     * loop mode, {@link StringParser::_charactersSearch _charactersSearch}
     * is evaluated before this variable.
     *
     * If in strict mode, parsing is stopped if a character that is not
     * allowed is encountered. If not in strict mode, the character is
     * simply ignored.
     *
     * @var array
     */
    public $_charactersAllowed = [];

    /**
     * Current parser status.
     *
     * @var int
     */
    public $_status = 0;

    /**
     * Prefilters.
     *
     * @var array
     */
    public $_prefilters = [];

    /**
     * Postfilters.
     *
     * @var array
     */
    public $_postfilters = [];

    /**
     * Recently reparsed?
     *
     * @var bool
     */
    public $_recentlyReparsed = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Constructor's functionality here, if you have any.
    }

    public function StringParser()
    {
        self::__construct();
    }

    /**
     * Add a filter.
     *
     * @param int   $type     The type of the filter
     * @param mixed $callback The callback to call
     *
     * @return bool
     *
     * @see STRINGPARSER_FILTER_PRE, STRINGPARSER_FILTER_POST
     */
    public function addFilter($type, $callback)
    {
        // make sure the function is callable
        if (!is_callable($callback)) {
            return false;
        }

        switch ($type) {
            case STRINGPARSER_FILTER_PRE:
                $this->_prefilters[] = $callback;
                break;
            case STRINGPARSER_FILTER_POST:
                $this->_postfilters[] = $callback;
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * Remove all filters.
     *
     * @param int $type The type of the filter or 0 for all
     *
     * @return bool
     *
     * @see STRINGPARSER_FILTER_PRE, STRINGPARSER_FILTER_POST
     */
    public function clearFilters($type = 0)
    {
        switch ($type) {
            case 0:
                $this->_prefilters = [];
                $this->_postfilters = [];
                break;
            case STRINGPARSER_FILTER_PRE:
                $this->_prefilters = [];
                break;
            case STRINGPARSER_FILTER_POST:
                $this->_postfilters = [];
                break;
            default:
                return false;
        }
        return true;
    }

    /**
     * This function parses the text.
     *
     * @param string $text The text to parse
     *
     * @return mixed Either the root object of the tree if no output method
     *               is defined, the tree reoutput to e.g. a string or false
     *               if an internal error occured, such as a parse error if
     *               in strict mode or the object is already parsing a text.
     */
    public function parse($text)
    {
        if ($this->_parsing) {
            return false;
        }
        $this->_parsing = true;
        $this->_text = $this->_applyPrefilters($text);
        $this->_output = null;
        $this->_length = strlen($this->_text);
        $this->_cpos = 0;
        unset($this->_stack);
        $this->_stack = [];
        if (is_object($this->_root)) {
            StringParser_Node::destroyNode($this->_root);
        }
        unset($this->_root);
        $this->_root = new StringParser_Node_Root();
        $this->_stack[0] = $this->_root;

        $this->_parserInit();

        $finished = false;

        while (!$finished) {
            switch ($this->_parserMode) {
                case STRINGPARSER_MODE_SEARCH:
                    $res = $this->_searchLoop();
                    if (!$res) {
                        $this->_parsing = false;
                        return false;
                    }
                    break;
                case STRINGPARSER_MODE_LOOP:
                    $res = $this->_loop();
                    if (!$res) {
                        $this->_parsing = false;
                        return false;
                    }
                    break;
                default:
                    $this->_parsing = false;
                    return false;
            }

            $res = $this->_closeRemainingBlocks();
            if (!$res) {
                if ($this->strict) {
                    $this->_parsing = false;
                    return false;
                } else {
                    $res = $this->_reparseAfterCurrentBlock();
                    if (!$res) {
                        $this->_parsing = false;
                        return false;
                    }
                    continue;
                }
            }
            $finished = true;
        }

        $res = $this->_modifyTree();

        if (!$res) {
            $this->_parsing = false;
            return false;
        }

        $res = $this->_outputTree();

        if (!$res) {
            $this->_parsing = false;
            return false;
        }

        if (is_null($this->_output)) {
            $root = $this->_root;
            unset($this->_root);
            $this->_root = null;
            while (count($this->_stack)) {
                unset($this->_stack[count($this->_stack) - 1]);
            }
            $this->_stack = [];
            $this->_parsing = false;
            return $root;
        }

        $res = StringParser_Node::destroyNode($this->_root);
        if (!$res) {
            $this->_parsing = false;
            return false;
        }
        unset($this->_root);
        $this->_root = null;
        while (count($this->_stack)) {
            unset($this->_stack[count($this->_stack) - 1]);
        }
        $this->_stack = [];

        $this->_parsing = false;
        return $this->_output;
    }

    /**
     * Apply prefilters.
     *
     * It is possible to specify prefilters for the parser to do some
     * manipulating of the string beforehand.
     */
    public function _applyPrefilters($text)
    {
        foreach ($this->_prefilters as $filter) {
            if (is_callable($filter)) {
                $ntext = call_user_func($filter, $text);
                if (is_string($ntext)) {
                    $text = $ntext;
                }
            }
        }
        return $text;
    }

    /**
     * Apply postfilters.
     *
     * It is possible to specify postfilters for the parser to do some
     * manipulating of the string afterwards.
     */
    public function _applyPostfilters($text)
    {
        foreach ($this->_postfilters as $filter) {
            if (is_callable($filter)) {
                $ntext = call_user_func($filter, $text);
                if (is_string($ntext)) {
                    $text = $ntext;
                }
            }
        }
        return $text;
    }

    /**
     * Abstract method: Manipulate the tree.
     *
     * @return bool
     */
    public function _modifyTree()
    {
        return true;
    }

    /**
     * Abstract method: Output tree.
     *
     * @return bool
     */
    public function _outputTree()
    {
        // this could e.g. call _applyPostfilters
        return true;
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
        // this should definitely not happen!
        if (($stack_count = count($this->_stack)) < 2) {
            return false;
        }
        $topelem = $this->_stack[$stack_count - 1];

        $node_parent = $topelem->_parent;
        // remove the child from the tree
        $res = $node_parent->removeChild($topelem, false);
        if (!$res) {
            return false;
        }
        $res = $this->_popNode();
        if (!$res) {
            return false;
        }

        // now try to get the position of the object
        if ($topelem->occurredAt < 0) {
            return false;
        }
        // HACK: could it be necessary to set a different status
        // if yes, how should this be achieved? Another member of
        // StringParser_Node?
        $this->_setStatus(0);
        $res = $this->_appendText($this->_text[$topelem->occurredAt]);
        if (!$res) {
            return false;
        }

        $this->_cpos = $topelem->occurredAt + 1;
        $this->_recentlyReparsed = true;

        return true;
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
        // not everything closed
        if ($this->strict) {
            return false;
        }
        while (count($this->_stack) > 1) {
            $res = $this->_popNode();
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    /**
     * Abstract method: Initialize the parser.
     */
    public function _parserInit()
    {
        $this->_setStatus(0);
    }

    /**
     * Abstract method: Set a specific status.
     */
    public function _setStatus($status)
    {
        if (0 !== (int)$status) {
            return false;
        }
        $this->_charactersSearch = [];
        $this->_charactersAllowed = [];
        $this->_status = $status;
        return true;
    }

    /**
     * Abstract method: Handle status.
     *
     * @param int    $status The current status
     * @param string $needle The needle that was found
     *
     * @return bool
     */
    public function _handleStatus($status, $needle)
    {
        $this->_appendText($needle);
        $this->_cpos += strlen($needle);
        return true;
    }

    /**
     * Search mode loop.
     *
     * @return bool
     */
    public function _searchLoop()
    {
        $i = 0;
        while (1) {
            // make sure this is false!
            $this->_recentlyReparsed = false;

            list($needle, $offset) = $this->_strpos($this->_charactersSearch, $this->_cpos);
            // parser ends here
            if (false === $needle) {
                // original status 0 => no problem
                if (!$this->_status) {
                    break;
                }
                // not in original status? strict mode?
                if ($this->strict) {
                    return false;
                }
                // break up parsing operation of current node
                $res = $this->_reparseAfterCurrentBlock();
                if (!$res) {
                    return false;
                }
                continue;
            }
            // get subtext
            $subtext = substr($this->_text, $this->_cpos, $offset - $this->_cpos);
            $res = $this->_appendText($subtext);
            if (!$res) {
                return false;
            }
            $this->_cpos = $offset;
            $res = $this->_handleStatus($this->_status, $needle);
            if (!$res && $this->strict) {
                return false;
            }
            if (!$res) {
                $res = $this->_appendText($this->_text[$this->_cpos]);
                if (!$res) {
                    return false;
                }
                ++$this->_cpos;
                continue;
            }
            if ($this->_recentlyReparsed) {
                $this->_recentlyReparsed = false;
                continue;
            }
            $this->_cpos += strlen($needle);
        }

        // get subtext
        if ($this->_cpos < strlen($this->_text)) {
            $subtext = substr($this->_text, $this->_cpos);
            $res = $this->_appendText($subtext);
            if (!$res) {
                return false;
            }
        }

        return true;
    }

    /**
     * Loop mode loop.
     *
     * @return bool
     */
    public function _loop()
    {
        // HACK: This method ist not yet implemented correctly, the code below
        // DOES NOT WORK! Do not use!

        return false;
        /*
        while ($this->_cpos < $this->_length) {
            $needle = $this->_strDetect ($this->_charactersSearch, $this->_cpos);

            if ($needle === false) {
                // not found => see if character is allowed
                if (!in_array ($this->_text{$this->_cpos}, $this->_charactersAllowed)) {
                    if ($strict) {
                        return false;
                    }
                    // ignore
                    continue;
                }
                // lot's of FIXMES
                $res = $this->_appendText ($this->_text{$this->_cpos});
                if (!$res) {
                    return false;
                }
            }

            // get subtext
            $subtext = substr ($this->_text, $offset, $offset - $this->_cpos);
            $res = $this->_appendText ($subtext);
            if (!$res) {
                return false;
            }
            $this->_cpos = $subtext;
            $res = $this->_handleStatus ($this->_status, $needle);
            if (!$res && $strict) {
                return false;
            }
        }
        // original status 0 => no problem
        if (!$this->_status) {
            return true;
        }
        // not in original status? strict mode?
        if ($this->strict) {
            return false;
        }
        // break up parsing operation of current node
        $res = $this->_reparseAfterCurrentBlock ();
        if (!$res) {
            return false;
        }
        // this will not cause an infinite loop because
        // _reparseAfterCurrentBlock will increase _cpos by one!
        return $this->_loop ();
        */
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
        // default: call _appendToLastTextChild
        return $this->_appendToLastTextChild($text);
    }

    /**
     * Append text to last text child of current top parser stack node.
     *
     * @param string $text The text to append
     *
     * @return bool On success, the function returns true, else false
     */
    public function _appendToLastTextChild($text)
    {
        $scount = count($this->_stack);
        if (0 === $scount) {
            return false;
        }
        return $this->_stack[$scount - 1]->appendToLastTextChild($text);
    }

    /**
     * Searches {@link StringParser::_text _text} for every needle that is
     * specified by using the {@link PHP_MANUAL#strpos strpos} function. It
     * returns an associative array with the key <code>'needle'</code>
     * pointing at the string that was found first and the key
     * <code>'offset'</code> pointing at the offset at which the string was
     * found first. If no needle was found, the <code>'needle'</code>
     * element is <code>false</code> and the <code>'offset'</code> element
     * is <code>-1</code>.
     *
     * @param array $needles
     * @param int   $offset
     *
     * @return array
     *
     * @see StringParser::_text
     */
    public function _strpos($needles, $offset)
    {
        $cur_needle = false;
        $cur_offset = -1;

        if ($offset < strlen($this->_text)) {
            foreach ($needles as $needle) {
                $n_offset = strpos($this->_text, $needle, $offset);
                if (false !== $n_offset && ($n_offset < $cur_offset || $cur_offset < 0)) {
                    $cur_needle = $needle;
                    $cur_offset = $n_offset;
                }
            }
        }

        return [$cur_needle, $cur_offset, 'needle' => $cur_needle, 'offset' => $cur_offset];
    }

    /**
     * Detects a string at the current position.
     *
     * @param array $needles The strings that are to be detected
     * @param int   $offset  The current offset
     *
     * @return mixed The string that was detected or the needle
     */
    public function _strDetect($needles, $offset)
    {
        foreach ($needles as $needle) {
            $l = strlen($needle);
            if (substr($this->_text, $offset, $l) === $needle) {
                return $needle;
            }
        }
        return false;
    }

    /**
     * Adds a node to the current parse stack.
     *
     * @param object $node The node that is to be added
     *
     * @return bool true on success, else false
     *
     * @see StringParser_Node, StringParser::_stack
     */
    public function _pushNode(&$node)
    {
        $stack_count = count($this->_stack);
        $max_node = $this->_stack[$stack_count - 1];
        if (!$max_node->appendChild($node)) {
            return false;
        }
        $this->_stack[$stack_count] = $node;
        return true;
    }

    /**
     * Removes a node from the current parse stack.
     *
     * @return bool true on success, else false
     *
     * @see StringParser_Node, StringParser::_stack
     */
    public function _popNode()
    {
        $stack_count = count($this->_stack);
        unset($this->_stack[$stack_count - 1]);
        return true;
    }

    /**
     * Execute a method on the top element.
     *
     * @return mixed
     */
    public function _topNode()
    {
        $args = func_get_args();
        if (!count($args)) {
            return; // oops?
        }
        $method = array_shift($args);
        $stack_count = count($this->_stack);
        $method = [&$this->_stack[$stack_count - 1], $method];
        if (!is_callable($method)) {
            return; // oops?
        }
        return call_user_func_array($method, $args);
    }

    /**
     * Get a variable of the top element.
     *
     * @return mixed
     */
    public function _topNodeVar($var)
    {
        $stack_count = count($this->_stack);
        return $this->_stack[$stack_count - 1]->$var;
    }
}

/*
 * Node type: Unknown node
 * @see StringParser_Node::_type
 */
define('STRINGPARSER_NODE_UNKNOWN', 0);

/*
 * Node type: Root node
 * @see StringParser_Node::_type
 */
define('STRINGPARSER_NODE_ROOT', 1);

/*
 * Node type: Text node
 * @see StringParser_Node::_type
 */
define('STRINGPARSER_NODE_TEXT', 2);

/*
 * Global value that is a counter of string parser node ids. Compare it to a
 * sequence in databases.
 * @var int
 */
$GLOBALS['__STRINGPARSER_NODE_ID'] = 0;

/**
 * Generic string parser node class.
 *
 * This is an abstract class for any type of node that is used within the
 * string parser. General warning: This class contains code regarding references
 * that is very tricky. Please do not touch this code unless you exactly know
 * what you are doing. Incorrect handling of references may cause PHP to crash
 * with a segmentation fault! You have been warned.
 */
class StringParser_Node
{
    /**
     * The type of this node.
     *
     * There are three standard node types: root node, text node and unknown
     * node. All node types are integer constants. Any node type of a
     * subclass must be at least 32 to allow future developements.
     *
     * @var int
     *
     * @see STRINGPARSER_NODE_ROOT, STRINGPARSER_NODE_TEXT
     * @see STRINGPARSER_NODE_UNKNOWN
     */
    public $_type = STRINGPARSER_NODE_UNKNOWN;

    /**
     * The node ID.
     *
     * This ID uniquely identifies this node. This is needed when searching
     * for a specific node in the children array. Please note that this is
     * only an internal variable and should never be used - not even in
     * subclasses and especially not in external data structures. This ID
     * has nothing to do with any type of ID in HTML oder XML.
     *
     * @var int
     *
     * @see StringParser_Node::_children
     */
    public $_id = -1;

    /**
     * The parent of this node.
     *
     * It is either null (root node) or a reference to the parent object.
     *
     * @var mixed
     *
     * @see StringParser_Node::_children
     */
    public $_parent = null;

    /**
     * The children of this node.
     *
     * It contains an array of references to all the children nodes of this
     * node.
     *
     * @var array
     *
     * @see StringParser_Node::_parent
     */
    public $_children = [];

    /**
     * Occured at.
     *
     * This defines the position in the parsed text where this node occurred
     * at. If -1, this value was not possible to be determined.
     *
     * @var int
     */
    public $occurredAt = -1;

    /**
     * Constructor.
     *
     * Currently, the constructor only allocates a new ID for the node and
     * assigns it.
     *
     * @param int $occurredAt The position in the text where this node
     *                        occurred at. If not determinable, it is -1.
     *
     * @global __STRINGPARSER_NODE_ID
     */
    public function __construct($occurredAt = -1)
    {
        // Constructor's functionality here, if you have any.
        $this->_id = $GLOBALS['__STRINGPARSER_NODE_ID']++;
        $this->occurredAt = $occurredAt;
    }

    public function StringParser_Node($occurredAt = -1)
    {
        self::__construct($occurredAt = -1);
    }

    /**
     * Type of the node.
     *
     * This function returns the type of the node
     *
     * @return int
     */
    public function type()
    {
        return $this->_type;
    }

    /**
     * Prepend a node.
     *
     * @param object $node the node to be prepended
     *
     * @return bool on success, the function returns true, else false
     */
    public function prependChild(&$node)
    {
        if (!is_object($node)) {
            return false;
        }

        // root nodes may not be children of other nodes!
        if (STRINGPARSER_NODE_ROOT === $node->_type) {
            return false;
        }

        // if node already has a parent
        if (false !== $node->_parent) {
            // remove node from there
            $parent = $node->_parent;
            if (!$parent->removeChild($node, false)) {
                return false;
            }
            unset($parent);
        }

        $index = count($this->_children) - 1;
        // move all nodes to a new index
        while ($index >= 0) {
            // save object
            $object = $this->_children[$index];
            // we have to unset it because else it will be
            // overridden in in the loop
            unset($this->_children[$index]);
            // put object to new position
            $this->_children[$index + 1] = $object;
            --$index;
        }
        $this->_children[0] = $node;
        return true;
    }

    /**
     * Append text to last text child.
     *
     * @param string $text The text to append
     *
     * @return bool On success, the function returns true, else false
     */
    public function appendToLastTextChild($text)
    {
        $ccount = count($this->_children);
        if (0 === $ccount || STRINGPARSER_NODE_TEXT !== $this->_children[$ccount - 1]->_type) {
            $ntextnode = new StringParser_Node_Text($text);
            return $this->appendChild($ntextnode);
        } else {
            $this->_children[$ccount - 1]->appendText($text);
            return true;
        }
    }

    /**
     * Append a node to the children.
     *
     * This function appends a node to the children array(). It
     * automatically sets the {@link StrinParser_Node::_parent _parent}
     * property of the node that is to be appended.
     *
     * @param object $node the node that is to be appended
     *
     * @return bool on success, the function returns true, else false
     */
    public function appendChild(&$node)
    {
        if (!is_object($node)) {
            return false;
        }

        // root nodes may not be children of other nodes!
        if (STRINGPARSER_NODE_ROOT === $node->_type) {
            return false;
        }

        // if node already has a parent
        if (null !== $node->_parent) {
            // remove node from there
            $parent = $node->_parent;
            if (!$parent->removeChild($node, false)) {
                return false;
            }
            unset($parent);
        }

        // append it to current node
        $new_index = count($this->_children);
        $this->_children[$new_index] = $node;
        $node->_parent = $this;
        return true;
    }

    /**
     * Insert a node before another node.
     *
     * @param object $node      the node to be inserted
     * @param object $reference the reference node where the new node is
     *                          to be inserted before
     *
     * @return bool on success, the function returns true, else false
     */
    public function insertChildBefore(&$node, &$reference)
    {
        if (!is_object($node)) {
            return false;
        }

        // root nodes may not be children of other nodes!
        if (STRINGPARSER_NODE_ROOT === $node->_type) {
            return false;
        }

        // is the reference node a child?
        $child = $this->_findChild($reference);

        if (false === $child) {
            return false;
        }

        // if node already has a parent
        if (null !== $node->_parent) {
            // remove node from there
            $parent = $node->_parent;
            if (!$parent->removeChild($node, false)) {
                return false;
            }
            unset($parent);
        }

        $index = count($this->_children) - 1;
        // move all nodes to a new index
        while ($index >= $child) {
            // save object
            $object = $this->_children[$index];
            // we have to unset it because else it will be
            // overridden in in the loop
            unset($this->_children[$index]);
            // put object to new position
            $this->_children[$index + 1] = $object;
            --$index;
        }
        $this->_children[$child] = $node;
        return true;
    }

    /**
     * Insert a node after another node.
     *
     * @param object $node      the node to be inserted
     * @param object $reference the reference node where the new node is
     *                          to be inserted after
     *
     * @return bool on success, the function returns true, else false
     */
    public function insertChildAfter(&$node, &$reference)
    {
        if (!is_object($node)) {
            return false;
        }

        // root nodes may not be children of other nodes!
        if (STRINGPARSER_NODE_ROOT === $node->_type) {
            return false;
        }

        // is the reference node a child?
        $child = $this->_findChild($reference);

        if (false === $child) {
            return false;
        }

        // if node already has a parent
        if (false !== $node->_parent) {
            // remove node from there
            $parent = $node->_parent;
            if (!$parent->removeChild($node, false)) {
                return false;
            }
            unset($parent);
        }

        $index = count($this->_children) - 1;
        // move all nodes to a new index
        while ($index >= $child + 1) {
            // save object
            $object = $this->_children[$index];
            // we have to unset it because else it will be
            // overridden in in the loop
            unset($this->_children[$index]);
            // put object to new position
            $this->_children[$index + 1] = $object;
            --$index;
        }
        $this->_children[$child + 1] = $node;
        return true;
    }

    /**
     * Remove a child node.
     *
     * This function removes a child from the children array. A parameter
     * tells the function whether to destroy the child afterwards or not.
     * If the specified node is not a child of this node, the function will
     * return false.
     *
     * @param mixed $child   the child to destroy; either an integer
     *                       specifying the index of the child or a reference
     *                       to the child itself
     * @param bool  $destroy destroy the child afterwards
     *
     * @return bool on success, the function returns true, else false
     */
    public function removeChild(&$child, $destroy = false)
    {
        if (is_object($child)) {
            // if object: get index
            $object = $child;
            unset($child);
            $child = $this->_findChild($object);
            if (false === $child) {
                return false;
            }
        } else {
            // remove reference on $child
            $save = $child;
            unset($child);
            $child = $save;

            // else: get object
            if (!isset($this->_children[$child])) {
                return false;
            }
            $object = $this->_children[$child];
        }

        // store count for later use
        $ccount = count($this->_children);

        // index out of bounds
        if (!is_int($child) || $child < 0 || $child >= $ccount) {
            return false;
        }

        // inkonsistency
        if (null === $this->_children[$child]->_parent ||
            $this->_children[$child]->_parent->_id !== $this->_id) {
            return false;
        }

        // $object->_parent = null would equal to $this = null
        // as $object->_parent is a reference to $this!
        // because of this, we have to unset the variable to remove
        // the reference and then redeclare the variable
        unset($object->_parent);
        $object->_parent = null;

        // we have to unset it because else it will be overridden in
        // in the loop
        unset($this->_children[$child]);

        // move all remaining objects one index higher
        while ($child < $ccount - 1) {
            // save object
            $obj = $this->_children[$child + 1];
            // we have to unset it because else it will be
            // overridden in in the loop
            unset($this->_children[$child + 1]);
            // put object to new position
            $this->_children[$child] = $obj;
            // UNSET THE OBJECT!
            unset($obj);
            ++$child;
        }

        if ($destroy) {
            return StringParser_Node::destroyNode($object);
            unset($object);
        }
        return true;
    }

    /**
     * Get the first child of this node.
     *
     * @return mixed
     */
    public function &firstChild()
    {
        $ret = null;
        if (!count($this->_children)) {
            return $ret;
        }
        return $this->_children[0];
    }

    /**
     * Get the last child of this node.
     *
     * @return mixed
     */
    public function &lastChild()
    {
        $ret = null;
        $c = count($this->_children);
        if (!$c) {
            return $ret;
        }
        return $this->_children[$c - 1];
    }

    /**
     * Destroy a node.
     *
     * @static
     *
     * @param object $node The node to destroy
     *
     * @return bool true on success, else false
     */
    public static function destroyNode(&$node)
    {
        if (null === $node) {
            return false;
        }
        // if parent exists: remove node from tree!
        if (null !== $node->_parent) {
            $parent = $node->_parent;
            // directly return that result because the removeChild
            // method will call destroyNode again
            return $parent->removeChild($node, true);
        }

        // node has children
        while (count($node->_children)) {
            $child = 0;
            // remove first child until no more children remain
            if (!$node->removeChild($child, true)) {
                return false;
            }
            unset($child);
        }

        // now call the nodes destructor
        if (!$node->_destroy()) {
            return false;
        }

        // now just unset it and prey that there are no more references
        // to this node
        unset($node);

        return true;
    }

    /**
     * Destroy this node.
     *
     * @return bool true on success, else false
     */
    public function _destroy()
    {
        return true;
    }

    /**
     * Find a child node.
     *
     * This function searches for a node in the own children and returns
     * the index of the node or false if the node is not a child of this
     * node.
     *
     * @param mixed $child the node to look for
     *
     * @return mixed the index of the child node on success, else false
     */
    public function _findChild(&$child)
    {
        if (!is_object($child)) {
            return false;
        }

        $ccount = count($this->_children);
        for ($i = 0; $i < $ccount; ++$i) {
            if ($this->_children[$i]->_id === $child->_id) {
                return $i;
            }
        }

        return false;
    }

    /**
     * Checks equality of this node and another node.
     *
     * @param mixed $node The node to be compared with
     *
     * @return bool true if the other node equals to this node, else false
     */
    public function equals(&$node)
    {
        return $this->_id === $node->_id;
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
        return false;
    }

    /**
     * Search for nodes with a certain criterium.
     *
     * This may be used to implement getElementsByTagName etc.
     *
     * @param string $criterium The criterium that is to be checked
     * @param mixed  $value     The value that is to be compared
     *
     * @return array All subnodes that match this criterium
     */
    public function &getNodesByCriterium($criterium, $value)
    {
        $nodes = [];
        $node_ctr = 0;
        for ($i = 0; $i < count($this->_children); ++$i) {
            if ($this->_children[$i]->matchesCriterium($criterium, $value)) {
                $nodes[$node_ctr++] = $this->_children[$i];
            }
            $subnodes = $this->_children[$i]->getNodesByCriterium($criterium, $value);
            if (count($subnodes)) {
                $subnodes_count = count($subnodes);
                for ($j = 0; $j < $subnodes_count; ++$j) {
                    $nodes[$node_ctr++] = $subnodes[$j];
                    unset($subnodes[$j]);
                }
            }
            unset($subnodes);
        }
        return $nodes;
    }

    /**
     * Search for nodes with a certain criterium and return the count.
     *
     * Similar to getNodesByCriterium
     *
     * @param string $criterium The criterium that is to be checked
     * @param mixed  $value     The value that is to be compared
     *
     * @return int The number of subnodes that match this criterium
     */
    public function getNodeCountByCriterium($criterium, $value)
    {
        $node_ctr = 0;
        for ($i = 0; $i < count($this->_children); ++$i) {
            if ($this->_children[$i]->matchesCriterium($criterium, $value)) {
                ++$node_ctr;
            }
            $subnodes = $this->_children[$i]->getNodeCountByCriterium($criterium, $value);
            $node_ctr += $subnodes;
        }
        return $node_ctr;
    }

    /**
     * Dump nodes.
     *
     * This dumps a tree of nodes
     *
     * @param string $prefix  The prefix that is to be used for indentation
     * @param string $linesep The line separator
     * @param int    $level   The initial level of indentation
     *
     * @return string
     */
    public function dump($prefix = ' ', $linesep = "\n", $level = 0)
    {
        $str = str_repeat($prefix, $level).$this->_id.': '.$this->_dumpToString().$linesep;
        for ($i = 0; $i < count($this->_children); ++$i) {
            $str .= $this->_children[$i]->dump($prefix, $linesep, $level + 1);
        }
        return $str;
    }

    /**
     * Dump this node to a string.
     *
     * @return string
     */
    public function _dumpToString()
    {
        if (STRINGPARSER_NODE_ROOT === $this->_type) {
            return 'root';
        }
        return (string) $this->_type;
    }
}

/**
 * String parser root node class.
 */
class StringParser_Node_Root extends StringParser_Node
{
    /**
     * The type of this node.
     *
     * This node is a root node.
     *
     * @var int
     *
     * @see STRINGPARSER_NODE_ROOT
     */
    public $_type = STRINGPARSER_NODE_ROOT;
}

/**
 * String parser text node class.
 */
class StringParser_Node_Text extends StringParser_Node
{
    /**
     * The type of this node.
     *
     * This node is a text node.
     *
     * @var int
     *
     * @see STRINGPARSER_NODE_TEXT
     */
    public $_type = STRINGPARSER_NODE_TEXT;

    /**
     * Node flags.
     *
     * @var array
     */
    public $_flags = [];

    /**
     * The content of this node.
     *
     * @var string
     */
    public $content = '';

    /**
     * Constructor.
     *
     * @param string $content    The initial content of this element
     * @param int    $occurredAt The position in the text where this node
     *                           occurred at. If not determinable, it is -1.
     *
     * @see StringParser_Node_Text::content
     */
    public function __construct($content, $occurredAt = -1)
    {
        parent::StringParser_Node($occurredAt);
        $this->content = $content;
    }

    public function StringParser_Node_Text($content, $occurredAt = -1)
    {
        // PHP4-style constructor.
        // This will NOT be invoked, unless a sub-class that extends `foo` calls it.
        // In that case, call the new-style constructor to keep compatibility.
        self::__construct($content, $occurredAt = -1);
    }

    /**
     * Append text to content.
     *
     * @param string $text The text to append
     *
     * @see StringParser_Node_Text::content
     */
    public function appendText($text)
    {
        $this->content .= $text;
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
     * Get Flag.
     *
     * @param string $flag    The requested flag
     * @param string $type    The requested type of the return value
     * @param mixed  $default The default return value
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
     * Dump this node to a string.
     */
    public function _dumpToString()
    {
        return 'text "'.substr(preg_replace('/\s+/', ' ', $this->content), 0, 40).'" [f:'.preg_replace('/\s+/', ' ', join(':', array_keys($this->_flags))).']';
    }
}
