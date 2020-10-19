<?php 
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; ?>
<blockquote class="decoda-quote">
    <?php if (!empty($author) || !empty($date)) { ?>
        <div class="decoda-quote-head">
            <?php if (!empty($date)) { ?>
                <span class="decoda-quote-date">
                    <?php echo date($dateFormat, is_numeric($date) ? $date : strtotime($date)); ?>
                </span>
            <?php }

            if (!empty($author)) { ?>
                <span class="decoda-quote-author">
                    <?php echo $this->getFilter()->message('quoteBy', array(
                        'author' => $this->escape($author)
                    )); ?>
                </span>
            <?php } ?>

            <span class="clear"></span>
        </div>
    <?php } ?>

    <div class="decoda-quote-body">
        <?php echo $content; ?>
    </div>
</blockquote>