<?php
/**
* $Id: edit.php 140 2011-01-11 08:11:30Z kapsl $
* @copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
* @license GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//echo $this->form->getValue('automate_sort_column');die;

?>

<script type="text/javascript">
function checkics(val){
if(val == 0){
jQuery('.location').hide();
jQuery('.normalshows').show();
// jQuery('#jform_location').removeClass('required');
// jQuery('#jform_location').removeAttr( "required" );

// jQuery('#jform_summary').removeClass('required');
// jQuery('#jform_summary').removeAttr( "required" );




jQuery('#jform_icsfilename').removeClass('required');
jQuery('#jform_icsfilename').removeAttr( "required" );

jQuery('#jform_displayname').removeClass('required');
jQuery('#jform_displayname').removeAttr( "required" );

jQuery('#jform_email').removeClass('required');
jQuery('#jform_email').removeClass( "validate-email" );
jQuery('#jform_email').removeAttr( "required" );





jQuery('#jform_adminemailsubject').removeClass('required');
jQuery('#jform_adminemailsubject').removeAttr( "required" );

jQuery('#jform_useremailsubject').removeClass('required');
jQuery('#jform_useremailsubject').removeAttr( "required" );

jQuery('#jform_useremailtext').removeClass('required');
jQuery('#jform_useremailtext').removeAttr( "required" );


jQuery('#jform_adminemailtext').removeClass('required');
jQuery('#jform_adminemailtext').removeAttr( "required" );


jQuery('#jform_hours').removeClass('required');
jQuery('#jform_hours').removeAttr( "required" );



}else{
jQuery('.location').show();
jQuery('.normalshows').hide();
// jQuery('#jform_location').addClass('required');
// jQuery('#jform_location').attr( "required","required" );

// jQuery('#jform_summary').addClass('required');
// jQuery('#jform_summary').attr( "required","required" );




jQuery('#jform_icsfilename').addClass('required');
jQuery('#jform_icsfilename').attr( "required","required" );

jQuery('#jform_displayname').addClass('required');
jQuery('#jform_displayname').attr( "required","required" );

jQuery('#jform_email').addClass('required');
jQuery('#jform_email').addClass('validate-email');
jQuery('#jform_email').attr( "required","required" );



jQuery('#jform_adminemailsubject').addClass('required');
jQuery('#jform_adminemailsubject').attr( "required","required" );

jQuery('#jform_useremailsubject').addClass('required');
jQuery('#jform_useremailsubject').attr( "required","required" );

jQuery('#jform_useremailtext').addClass('required');
jQuery('#jform_useremailtext').attr( "required","required" );

jQuery('#jform_adminemailtext').addClass('required');
jQuery('#jform_adminemailtext').attr( "required","required" );


jQuery('#jform_hours').addClass('required');
jQuery('#jform_hours').attr(  "required","required" );

}


}
Joomla.submitbutton = function(task)
{
if (task == 'etetable.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
Joomla.submitform(task, document.getElementById('adminForm'));
}
else {
alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
}
}
</script>
<style>
#jform_useremailtext_ifr,#jform_aftertext_ifr,#jform_pretext_ifr{
height: 125px !important;
}
#jform_commentary,#jform_commentary_ifr,#jform_adminemailtext,#jform_adminemailtext_ifr{
height: 125px !important;	
}
.editor{
width: 60%;
height: auto;
}
.editor .pull-right{
float: left;
margin-left: 5px;
}
<?php
if($this->item->show_pagination == 1){ ?>
.pagebreak{display:block;list-style: none;}
<?php }else{ ?>
.pagebreak{display:none;list-style: none;}
<?php }
?>
}
</style>



<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="span10 form-horizontal">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EVENTTABLEEDIT_NEW_ETETABLE') : JText::sprintf('COM_EVENTTABLEEDIT_EDIT_ETETABLE', $this->item->id); ?></a></li>
			<li><a href="#style" data-toggle="tab"><?php echo JText::_('COM_EVENTTABLEEDIT_STYLE');?></a></li>
			<li><a href="#meta" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS');?></a></li>
			<li><a href="#rules" data-toggle="tab"><?php echo JText::_('COM_EVENTTABLEEDIT_FIELDSET_RULES');?></a></li>
		</ul>
		<div class="tab-content">
			<div  class="tab-pane active" id="general">
				<fieldset class="adminform">
					<legend><?php echo empty($this->item->id) ? JText::_('COM_EVENTTABLEEDIT_NEW_ETETABLE') : JText::sprintf('COM_EVENTTABLEEDIT_EDIT_ETETABLE', $this->item->id); ?></legend>
					<ul class="adminformlist">
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="field"><?php echo $this->form->getInput('name'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('alias'); ?></div>
							<div class="field"><?php echo $this->form->getInput('alias'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('access'); ?></div>
							<div class="field"><?php echo $this->form->getInput('access'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('published'); ?></div>
							<div class="field"><?php echo $this->form->getInput('published'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('language'); ?></div>
							<div class="field"><?php echo $this->form->getInput('language'); ?></div>
						</li>
						<!--
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('normalorappointment'); ?></div>
							<div class="field"><?php echo $this->form->getInput('normalorappointment'); ?></div>
						</li>
						-->
						<li class="normalshows">
							<div class="fieldlabel"><?php echo $this->form->getLabel('sorting'); ?></div>
							<div class="field"><?php echo $this->form->getInput('sorting'); ?></div>
						</li>
						<?php if($this->id){ ?>
						<!--
						<li class="automate_sort">
							<div class="fieldlabel"><?php echo $this->form->getLabel('automate_sort'); ?></div>
							<div class="field"><?php echo $this->form->getInput('automate_sort'); ?></div>
						</li>
						-->
						<li class="automate_sort">
							<div class="fieldlabel">
								<label id="jform_automate_sort-lbl" for="jform_automate_sort" class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_ENABLE_AUTOMATIC_SORTING_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_ENABLE_AUTOMATIC_SORTING_LABEL'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_ENABLE_AUTOMATIC_SORTING_LABEL'); ?></label>
							</div>
							<div class="field">
								<fieldset id="jform_automate_sort" class="radio">
									<input type="radio" id="jform_automate_sort0" name="jform[automate_sort]" value="1" <?php if($this->item->automate_sort == 1){?> checked="checked" <?php } ?> aria-invalid="false">
									<label for="jform_automate_sort0">Yes</label>
									<li class="automate_sort_column" style="<?php if(!$this->form->getValue('automate_sort') || !$this->id){?>display:none;<?php } ?> list-style:none;">
										<label id="jform_automate_sort_column-lbl" for="jform_automate_sort_column" class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_CHOOSE_COLUMN_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_CHOOSE_COLUMN_LABEL'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_CHOOSE_COLUMN_LABEL'); ?></label>
										<fieldset id="" class="select">
											<?php
											if(!empty($this->fields)){
											?>
											<select id="jform_automate_sort_column" name="jform[automate_sort_column]">
												<?php
												$updown = array("asc","desc");
												$updown_html = array("asc"=>"&uarr;","desc"=>"&darr;");
												foreach($this->fields as $re){
													foreach($updown as $ud){
														?>
														<option value="head_<?php echo $re->id?>,<?php echo $ud?>" <?php if($this->form->getValue('automate_sort_column') == 'head_'.$re->id.','.$ud){ echo "selected=selected";}?>><?php echo $re->name?> <?php echo $updown_html[$ud];?></option>
													<?php
													}
												}?>
												<option <?php if($this->form->getValue('automate_sort_column') == 'timestamp,asc'){ echo "selected=selected";}?> value="timestamp,asc">Timestamp &uarr;</option>
												<option <?php if($this->form->getValue('automate_sort_column') == 'timestamp,desc'){ echo "selected=selected";}?> value="timestamp,desc">Timestamp &darr;</option>
											</select>
											<?php } ?>
										</fieldset>
									</li>
									<input type="radio" id="jform_automate_sort1" name="jform[automate_sort]" value="0" <?php if($this->item->automate_sort == 0){?> checked="checked" <?php } ?> aria-invalid="false">
									<label for="jform_automate_sort1">No</label>
								</fieldset>
							</div>
						</li>
						<?php } ?>
						<li class="normalshows">
							<div class="fieldlabel"><?php echo $this->form->getLabel('switcher'); ?></div>
							<div class="field"><?php echo $this->form->getInput('switcher'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('standardlayout'); ?></div>
							<div class="field"><?php echo $this->form->getInput('standardlayout'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel"><?php echo $this->form->getLabel('hours'); ?></div>
							<div class="field"><?php echo $this->form->getInput('hours'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel"><?php echo $this->form->getLabel('showdayname'); ?></div>
							<div class="field"><?php echo $this->form->getInput('showdayname'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_icsfilename" id="jform_icsfilename-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ICSFILENAME_LABEL'); ?>" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ICSFILENAME_DESC'); ?>">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ICSFILENAME_LABEL'); ?><span class="star">&nbsp;*</span></label>
								<?php echo JText::_('COM_EVENTTABLEEDIT_USED_VARIABLE_IN_ICS_SUBJECT'); ?>
								<br>
								<!--<?php echo $this->form->getLabel('icsfilename'); ?>-->
							</div>
							<div class="field"><?php echo $this->form->getInput('icsfilename'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel"><?php echo $this->form->getLabel('location'); ?></div>
							<div class="field"><?php echo $this->form->getInput('location'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel"><?php echo $this->form->getLabel('summary'); ?></div>
							<div class="field"><?php echo $this->form->getInput('summary'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_displayname" id="jform_displayname-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_DISPLAYNAME_LABEL'); ?>" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_DISPLAYNAME_DESC'); ?>">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_DISPLAYNAME_LABEL'); ?><span class="star">&nbsp;*</span></label>
								<!--<?php echo $this->form->getLabel('displayname'); ?>-->
							</div>
							<div class="field"><?php echo $this->form->getInput('displayname'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_email" id="jform_email-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_EMAIL_LABEL'); ?>" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_EMAIL_DESC'); ?>">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_EMAIL_LABEL'); ?><span class="star">&nbsp;*</span></label>
								<!--<?php echo $this->form->getLabel('email'); ?>-->
							</div>
							<div class="field"><?php echo $this->form->getInput('email'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_adminemailsubject" id="jform_adminemailsubject-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ADMINEMAIL_LABEL'); ?>" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ADMINEMAIL_DESC'); ?>">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ADMINEMAIL_LABEL'); ?><span class="star">&nbsp;*</span></label>
								<!--<?php echo $this->form->getLabel('adminemailsubject'); ?>-->
								<?php echo JText::_('COM_EVENTTABLEEDIT_USED_VARIABLE_IN_ADMIN_EMAIL_SUBJECT'); ?>
							</div>
							<div class="field"><?php echo $this->form->getInput('adminemailsubject'); ?></div>
						</li>
						<li class="location" >
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_useremailsubject" id="jform_useremailsubject-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_USEREMAIL_SUBJECT_LABEL'); ?>" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_USEREMAIL_SUBJECT_DESC'); ?>">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_USEREMAIL_SUBJECT_LABEL'); ?><span class="star">&nbsp;*</span></label>
								<!--<?php echo $this->form->getLabel('useremailsubject'); ?>-->
								<?php echo JText::_('COM_EVENTTABLEEDIT_USED_VARIABLE_IN_USED_EMAIL_SUBJECT'); ?>
							</div>
							<div class="field"><?php echo $this->form->getInput('useremailsubject'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_useremailtext" id="jform_useremailtext-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_USEREMAIL_TEXT'); ?>&lt;/strong&gt;">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_USEREMAIL_TEXT'); ?><span class="star">&nbsp;*</span></label>
								<!--<?php echo $this->form->getLabel('useremailtext'); ?>-->
								<?php echo JText::_('COM_EVENTTABLEEDIT_USED_VARIABLE_IN_USED_EMAIL_ADMIN'); ?>
							</div>
							<div class="field"><?php echo $this->form->getInput('useremailtext'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_adminemailtext" id="jform_adminemailtext-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ADMINEMAILTEXT_LABEL'); ?>&lt;/strong&gt;">
								<?php echo JText::_('COM_EVENTTABLEEDIT_FIELD_ADMINEMAILTEXT_LABEL'); ?><span class="star">&nbsp;*</span></label>
								<!--<?php echo $this->form->getLabel('adminemailtext'); ?>-->
								<?php echo JText::_('COM_EVENTTABLEEDIT_USED_VARIABLE_IN_ADMINUSED_EMAIL_SUBJECT'); ?>
							</div>
							<div class="field"><?php echo $this->form->getInput('adminemailtext'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel"><?php echo $this->form->getLabel('showusernametoadmin'); ?></div>
							<div class="field"><?php echo $this->form->getInput('showusernametoadmin'); ?></div>
						</li>
						<li class="location">
							<div class="fieldlabel"><?php echo $this->form->getLabel('showusernametouser'); ?></div>
							<div class="field"><?php echo $this->form->getInput('showusernametouser'); ?></div>
						</li>
						<?php if($this->item->id == ''){ ?>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('row'); ?></div>
							<div class="field"><?php echo $this->form->getInput('row'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('col'); ?></div>
							<div class="field"><?php echo $this->form->getInput('col'); ?></div>
						</li>
						<?php }else{ ?>
							<input type="hidden" aria-required="true" required="required" step="1" size="30" class="inputbox required" value="<?php echo $this->item->row; ?>" id="jform_row" name="jform[row]"></li>
							<input type="hidden" aria-required="true" required="required" step="1" size="30" class="inputbox required" value="<?php echo $this->item->col; ?>" id="jform_col" name="jform[col]"></li>

						<?php } ?>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('show_filter'); ?></div>
							<div class="field"><?php echo $this->form->getInput('show_filter'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('addtitle'); ?></div>
							<div class="field"><?php echo $this->form->getInput('addtitle'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('rowsort'); ?></div>
							<div class="field"><?php echo $this->form->getInput('rowsort'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('rowdelete'); ?></div>
							<div class="field"><?php echo $this->form->getInput('rowdelete'); ?></div>
						</li>
						<!--
						<li>
							<div class="fieldlabel"><?php //echo $this->form->getLabel('show_pagination'); ?></div>
							<div class="field"><?php //echo $this->form->getInput('show_pagination'); ?></div>
						</li>
						-->
						<li>
							<div class="fieldlabel">
								<label title="" class="hasPopover" for="jform_show_pagination" id="jform_show_pagination-lbl" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_SHOW_PAGINATION_LABEL'); ?>" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_SHOW_PAGINATION_DESC'); ?>">
							<?php echo JText::_('COM_EVENTTABLEEDIT_SHOW_PAGINATION_LABEL'); ?></label>
							</div>
							<div class="field">
								<fieldset class="inputbox radio" id="jform_show_pagination">
									<ul class="adminformlist">
										<li>
											<input type="radio" value="1" name="jform[show_pagination]" id="jform_show_pagination0" <?php if($this->item->show_pagination == 1){?> checked="checked" <?php } ?> onclick="jQuery('.pagebreak').show();">
											<label for="jform_show_pagination0"><?php echo JText::_('JSHOW'); ?></label>
										</li>
										<li class="pagebreak">
											<?php echo $this->form->getLabel('pagebreak'); ?>
											<?php echo $this->form->getInput('pagebreak'); ?>
										</li>
										<li>
											<input type="radio" value="0" name="jform[show_pagination]" id="jform_show_pagination1" <?php if($this->item->show_pagination == 0){?> checked="checked" <?php } ?> onclick="jQuery('.pagebreak').hide();">
											<label for="jform_show_pagination1"><?php echo JText::_('JHIDE'); ?></label>
										</li>
									</ul>
								</fieldset>
							</div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('show_first_row'); ?></div>
							<div class="field"><?php echo $this->form->getInput('show_first_row'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('show_print_view'); ?></div>
							<div class="field"><?php echo $this->form->getInput('show_print_view'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('bbcode'); ?></div>
							<div class="field"><?php echo $this->form->getInput('bbcode'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('bbcode_img'); ?></div>
							<div class="field"><?php echo $this->form->getInput('bbcode_img'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="field"><?php echo $this->form->getInput('id'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('pretext'); ?></div>
							<div class="field"><?php echo $this->form->getInput('pretext'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('aftertext'); ?></div>
							<div class="field"><?php echo $this->form->getInput('aftertext'); ?></div>
						</li>
					</ul>
				</fieldset>
			</div> 
			<div  id="style" class="tab-pane">
				<fieldset class="panelform">
					<ul class="adminformlist">
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('dateformat'); ?></div>
							<div class="field"><?php echo $this->form->getInput('dateformat'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('timeformat'); ?></div>
							<div class="field"><?php echo $this->form->getInput('timeformat'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('float_separator'); ?></div>
							<div class="field"><?php echo $this->form->getInput('float_separator'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('cellspacing'); ?></div>
							<div class="field"><?php echo $this->form->getInput('cellspacing'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('cellpadding'); ?></div>
							<div class="field"><?php echo $this->form->getInput('cellpadding'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('tablecolor1'); ?></div>
							<div class="field"><?php echo $this->form->getInput('tablecolor1'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('tablecolor2'); ?></div>
							<div class="field"><?php echo $this->form->getInput('tablecolor2'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('cellbreak'); ?></div>
							<div class="field"><?php echo $this->form->getInput('cellbreak'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('link_target'); ?></div>
							<div class="field"><?php echo $this->form->getInput('link_target'); ?></div>
						</li>
						<li>
							<div class="fieldlabel"><?php echo $this->form->getLabel('scroll_table'); ?></div>
							<div class="field"><?php echo $this->form->getInput('scroll_table'); ?></div>
						</li>
						<li id="scroll_table_height">
							<div class="fieldlabel"><?php echo $this->form->getLabel('scroll_table_height'); ?></div>
							<div class="field"><?php echo $this->form->getInput('scroll_table_height'); ?></div>
						</li>
					</ul>
				</fieldset>
			</div>
			<div  id="meta" class="tab-pane">
				<?php echo $this->loadTemplate('metadata'); ?>
			</div>
			<div id="rules" class="tab-pane">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('edit_own_rows'); ?>
					<?php echo $this->form->getInput('edit_own_rows'); ?>
					<div class="clr"></div>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
		</div>
	</div>
	<?php echo $this->form->getInput('temps'); ?>
	<input type="hidden" name="normalorappointment" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>
	<!-- added to resolve loading issue -->
	<input type="hidden" name="title" id="jform_title" value="" />
	</form>
<style>
.fieldlabel{
	width: 100%;
	float:left;
}
.fieldlabel label{
	float:left;
	padding-right: 20px;
}
.field{
	width: 100%;
	float:left;
}
.pagebreak label{
	width: 200px;
}
ul.adminformlist li {
    float: left;
    width: 100%;
    margin-bottom: 10px;
}

#scroll_table_height{
display: none;
}
.popover.right {
/* margin-left: 10px;
left: 800px!important; */
}
</style>
<script>
jQuery(document).ready(function(){
if(jQuery("#jform_scroll_table").val() == 1){
jQuery("#scroll_table_height").show();
}
jQuery("#jform_scroll_table").change(function(){
if(jQuery(this).val() == 1){
jQuery("#scroll_table_height").show();
}else{
jQuery("#scroll_table_height").hide();
}
})
})
</script>
<div class="clr"></div>
<?php 
if($this->item->id > 0){
?>
<script type="text/javascript">
checkics(<?php echo $this->item->normalorappointment ?>);
</script>
<?php 
}else{
?>
<script type="text/javascript">
checkics(0);
</script>
<?php 
}
?>
<script type="text/javascript">
jQuery(document).ready(function(){
jQuery("input[name='jform[automate_sort]']").change(function(){
if(jQuery(this).val()==1){
jQuery("li.automate_sort_column").show();
}else{
jQuery("li.automate_sort_column").hide();
}
});
});
</script>