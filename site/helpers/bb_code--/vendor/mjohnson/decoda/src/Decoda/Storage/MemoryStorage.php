<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
namespace Decoda\Storage;

use Decoda\Exception\MissingItemException;

/**
 * Cache data using in-memory.
 */
class MemoryStorage extends AbstractStorage {

    /**
     * Internal cache.
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * {@inheritdoc}
     */
    public function get($key) {
        if (!$this->has($key)) {
            throw new MissingItemException(sprintf('Item with key %s does not exist', $key));
        }

        return $this->_cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        return isset($this->_cache[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        unset($this->_cache[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expires) {
        $this->_cache[$key] = $value;

        return true;
    }

}
