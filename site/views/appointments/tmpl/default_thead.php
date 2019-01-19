<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Optional first row
 */
if ($this->item->show_first_row) :?>
	<th class="etetable-first_row">#</th>
<?php endif; ?>

<?php
/**
 * The table heads
 */
$thcount = 0;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
if(count($this->heads) > 6){
$cont = round(count($this->heads)/12);
}else if(count($this->heads) > 3 && count($this->heads) < 6){
$cont = round(count($this->heads)/6);
}else{
	$cont = 1;
}
$j=0;
$ars = 0;
foreach ($this->heads as $head) { 
	if($thcount == 0){
		$priority = "persist";
		$classofdynamic = "";
	}else{
		$priority = $thcount;
		$classofdynamic = 'tablesaw-priority-'.$priority;
	}
		
			if($classofdynamic==""){
				$myclass =  $thcount;
			}else{
				$myclass = $thcount.' '.$classofdynamic;
				}
				// add weekday in first row (head) //
				if($this->item->normalorappointment == 1 &&  $ars !=0 ){
					if($this->item->showdayname == 1){
						$namesofday = strtoupper(date('l',strtotime(str_replace('.', '-', trim($head->name)))));
						$datesofhead = JTEXT::_('COM_EVENTTABLEEDIT_'.strtoupper($namesofday)).', '.$head->name;
					}else{
						$datesofhead = $head->name;
					}
				}else{
					$datesofhead = trim($head->name);
				}
				// END add weekday in first row (head) //
?>
	<th class="evth<?php echo $myclass; ?>"  data-tablesaw-sortable-col="" data-tablesaw-priority="<?php echo $priority; ?>" scope="col"><?php 	echo $datesofhead;?></th>
	<?php
	
	if($j%$cont == 0){
	$thcount++;
	}
$j++;
$ars++;
}	
?>
