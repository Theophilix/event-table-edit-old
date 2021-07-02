<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Decoda\Hook;
defined('_JEXEC') or die;


use Decoda\Decoda;
use Decoda\Component\AbstractComponent;
use Decoda\Hook;

/**
 * A hook allows you to inject functionality during certain events in the parsing cycle.
 */
abstract class AbstractHook extends AbstractComponent implements Hook {

    /**
     * {@inheritdoc}
     */
    public function afterContent($content) {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function afterParse($content) {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function afterStrip($content) {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeContent($content) {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeParse($content) {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeStrip($content) {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function startup() {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function setupFilters(Decoda $decoda) {
        return $this;
    }

}