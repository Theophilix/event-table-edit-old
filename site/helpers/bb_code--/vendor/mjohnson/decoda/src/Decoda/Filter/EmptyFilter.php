<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
namespace Decoda\Filter;

/**
 * An empty filter for no operation events.
 */
class EmptyFilter extends AbstractFilter {

    /**
     * Supported tags.
     *
     * @type array
     */
    protected $_tags = array(
        'root' => array()
    );

}