<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Decoda;
defined('_JEXEC') or die;


/**
 * Defines the methods for all cache storage engines to implement.
 */
interface Storage {

    /**
     * Retrieve a value from the cache.
     *
     * @param string $key
     * @return string
     */
    public function get($key);

    /**
     * Check if a value exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Remove a value from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function remove($key);

    /**
     * Write a value to the cache.
     *
     * @param string $key
     * @param string $value
     * @param int $expires
     * @return bool
     */
    public function set($key, $value, $expires);

}
