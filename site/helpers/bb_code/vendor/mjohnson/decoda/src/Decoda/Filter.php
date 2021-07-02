<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Decoda;
defined('_JEXEC') or die;


/**
 * Defines the methods for all Filters to implement.
 */
interface Filter extends Component {

    /**
     * Regex patterns for attribute parsing.
     */
    const WILDCARD = '/(.*?)/';
    const ALPHA = '/^[a-z_\-\s]+$/i';
    const ALNUM = '/^[a-z0-9,_\s\.\-\+\/]+$/i';
    const NUMERIC = '/^[0-9,\.\-\+\/]+$/';

    /**
     * Return a tag if it exists, and merge with defaults.
     *
     * @param string $tag
     * @return array
     */
    public function getTag($tag);

    /**
     * Return all tags.
     *
     * @return array
     */
    public function getTags();

    /**
     * Parse the node and its content into an HTML tag.
     *
     * @param array $tag
     * @param string $content
     * @return string
     */
    public function parse(array $tag, $content);

    /**
     * Add any hook dependencies.
     *
     * @param \Decoda\Decoda $decoda
     * @return \Decoda\Filter
     */
    public function setupHooks(Decoda $decoda);

    /**
     * Strip a node and remove content dependent on settings.
     *
     * @param array $tag
     * @param string $content
     * @return string
     */
    public function strip(array $tag, $content);

}