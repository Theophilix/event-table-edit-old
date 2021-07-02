<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Decoda\Filter;
defined('_JEXEC') or die;


use Decoda\Decoda;

/**
 * Provides tags for ordered and unordered lists.
 */
class ListFilter extends AbstractFilter {

    const LIST_TYPE = '/^[-a-z]+$/i';

    /**
     * Supported tags.
     *
     * @type array
     */
    protected $_tags = array(
        'olist' => array(
            'htmlTag' => 'ol',
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_BOTH,
            'lineBreaks' => Decoda::NL_REMOVE,
            'childrenWhitelist' => array('li', '*'),
            'onlyTags' => true,
            'attributes' => array(
                'default' => array(self::LIST_TYPE, 'type-{default}')
            ),
            'mapAttributes' => array(
                'default' => 'class'
            ),
            'htmlAttributes' => array(
                'class' => 'decoda-olist'
            )
        ),
        'ol' => array(
            'aliasFor' => 'olist'
        ),
        'list' => array(
            'htmlTag' => 'ul',
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_BOTH,
            'lineBreaks' => Decoda::NL_REMOVE,
            'childrenWhitelist' => array('li', '*'),
            'onlyTags' => true,
            'attributes' => array(
                'default' => array(self::LIST_TYPE, 'type-{default}')
            ),
            'mapAttributes' => array(
                'default' => 'class'
            ),
            'htmlAttributes' => array(
                'class' => 'decoda-list'
            )
        ),
        'ul' => array(
            'aliasFor' => 'list'
        ),
        'li' => array(
            'htmlTag' => 'li',
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_BOTH,
            'parent' => array('olist', 'list', 'ol', 'ul')
        ),
        '*' => array(
            'htmlTag' => 'li',
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_BOTH,
            'childrenBlacklist' => array('olist', 'list', 'ol', 'ul', 'li'),
            'parent' => array('olist', 'list', 'ol', 'ul')
        )
    );

}
