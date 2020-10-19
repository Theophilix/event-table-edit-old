<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
namespace Decoda;

/**
 * Defines the methods for all resource Loaders to implement.
 */
interface Loader extends Component {

    /**
     * Load the resources contents.
     *
     * @return array
     */
    public function load();

}