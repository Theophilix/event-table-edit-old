<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/*
 * Optional first row
 */
if ($this->item->show_first_row) :?>
	<th class="etetable-first_row">#</th>
<?php endif; ?>

<?php
/**
 * The table heads.
 */
$thcount = 0;

if (count($this->heads) > 6) {
    $cont = round(count($this->heads) / 12);
} elseif (count($this->heads) > 3 && count($this->heads) < 6) {
    $cont = round(count($this->heads) / 6);
} else {
    $cont = 1;
}
$j = 0;
$ars = 0;
foreach ($this->heads as $head) {
    if (0 === (int)$thcount) {
        $priority = 'persist';
        $classofdynamic = '';
    } else {
        $priority = $thcount;
        $classofdynamic = 'tablesaw-priority-'.$priority;
    }

    if ('' === $classofdynamic) {
        $myclass = $thcount;
    } else {
        $myclass = $thcount.' '.$classofdynamic;
    }
    // add weekday in first row (head) //
    if (1 === (int)$this->item->normalorappointment && 0 !== (int)$ars) {
        if (1 === (int)$this->item->showdayname) {
            $namesofday = strtoupper(date('l', strtotime(str_replace('.', '-', trim($head->name)))));
            $datesofhead = JTEXT::_('COM_EVENTTABLEEDIT_'.strtoupper($namesofday)).', '.$head->name;
        } else {
            $datesofhead = $head->name;
        }
    } else {
        $datesofhead = trim($head->name);
    }
    // END add weekday in first row (head) // ?>
	<th class="evth<?php echo $myclass; ?>"  data-tablesaw-sortable-col="" data-tablesaw-priority="<?php echo $priority; ?>" scope="col"><?php 	echo $datesofhead; ?></th>
	<?php

    if (0 === (int)($j % $cont)) {
        ++$thcount;
    }
    ++$j;
    ++$ars;
}
?>
