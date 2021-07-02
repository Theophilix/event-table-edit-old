<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Decoda;
defined('_JEXEC') or die;


/**
 * Defines the methods for all Hooks to implement.
 */
interface Hook extends Component {

    /**
     * Process the node's inner content after the filters have finished.
     *
     * @param string $content
     * @return string
     */
    public function afterContent($content);

    /**
     * Process the content after the parsing has finished.
     *
     * @param string $content
     * @return string
     */
    public function afterParse($content);

    /**
     * Process the content after the stripping has finished.
     *
     * @param string $content
     * @return string
     */
    public function afterStrip($content);

    /**
     * Process the node's inner content before the filters are applied.
     *
     * @param string $content
     * @return string
     */
    public function beforeContent($content);

    /**
     * Process the content before the parsing begins.
     *
     * @param string $content
     * @return string
     */
    public function beforeParse($content);

    /**
     * Process the content before the stripping begins.
     *
     * @param string $content
     * @return string
     */
    public function beforeStrip($content);

    /**
     * Start up the Hook by initializing or loading any data before parsing begins.
     *
     * @return void
     */
    public function startup();

    /**
     * Add any filter dependencies.
     *
     * @param \Decoda\Decoda $decoda
     * @return \Decoda\Hook
     */
    public function setupFilters(Decoda $decoda);

}