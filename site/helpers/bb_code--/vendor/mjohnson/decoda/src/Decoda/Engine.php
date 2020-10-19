<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
namespace Decoda;

/**
 * This interface represents the rendering engine for tags that use a template.
 * It contains the path were the templates are located and the logic to render these templates.
 */
interface Engine extends Component {

    /**
     * Add a template lookup path.
     *
     * @param string $path
     * @return \Decoda\Engine
     */
    public function addPath($path);

    /**
     * Return the current filter.
     *
     * @return \Decoda\Filter
     */
    public function getFilter();

    /**
     * Returns the paths to the templates.
     *
     * @return string
     */
    public function getPaths();

    /**
     * Renders the tag by using the defined templates.
     *
     * @param array $tag
     * @param string $content
     * @return string
     */
    public function render(array $tag, $content);

    /**
     * Sets the current used filter.
     *
     * @param \Decoda\Filter $filter
     * @return \Decoda\Engine
     */
    public function setFilter(Filter $filter);

}
