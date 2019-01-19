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
	<th class="etetable-first_row tablesaw-priority-50">#</th>
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
//$cont = round((count($this->heads)+ $this->item->show_first_row)/6);
//$cont = 1;
$main  = JFactory::getApplication()->input;
$postget = $main->getArray($_REQUEST);
if(@$postget['sort']){
$sortdynamic = explode('_', $postget['sort']);
$sortdynamic = $sortdynamic[0];
}else{
	$sortdynamic = 0;
}
$j=0;
$doc = JFactory::getDocument();
foreach ($this->heads as $head) { 
	$sortcalss = '';
	if($head->datatype == 'text'){
		$sortcalss = 'custom-sort'.$head->id;	
	}
	/*if($head->head == 'link' || $head->head == 'mail'){
		$priority = "persist";
		$classofdynamic = "";
	}else */
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
?>
	<th class="evth<?php echo $myclass; ?>" id="<?php echo $sortcalss; ?>"  data-tablesaw-sortable-col="" <?php //if($j==$sortdynamic){ echo 'data-tablesaw-sortable-default-col="true"'; }  ?> data-tablesaw-priority="<?php echo $priority; ?>" scope="col"><?php 	echo trim($head->name);?></th>
	<?php
	
	if($j%$cont == 0){
	$thcount++;
	}
$j++;
}	
?>
<th class="evth<?php echo $myclass; ?>" id="timestamp-head"  data-tablesaw-sortable-col="" <?php //if($j==$sortdynamic){ echo 'data-tablesaw-sortable-default-col="true"'; }  ?> data-tablesaw-priority="<?php echo $priority; ?>" scope="col">Timestamp</th>
