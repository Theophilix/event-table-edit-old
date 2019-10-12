<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('metadata');

foreach ($fieldSets as $name => $fieldSet) :
//	echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name.'-options');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="panelform">
		<ul class="adminformlist">
			<?php if ($name == 'jmetadata') : // Include the real fields in this panel. ?>
				<li>
					<div class="fieldlabel"><?php echo $this->form->getLabel('metadesc'); ?></div>
					<div class="field"><?php echo $this->form->getInput('metadesc'); ?></div>
				</li>

				<li>
					<div class="fieldlabel"><?php echo $this->form->getLabel('metakey'); ?></div>
					<div class="field"><?php echo $this->form->getInput('metakey'); ?></div>
				</li>

				<!--li>
					<div class="fieldlabel"><?php echo $this->form->getLabel('xreference'); ?></div>
					<div class="field"><?php echo $this->form->getInput('xreference'); ?></div>
				</li>-->
			<?php endif; ?>
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<li>
					<div class="fieldlabel"><?php echo $field->label; ?></div>
					<div class="field"><?php echo $field->input; ?></div>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
<?php endforeach; ?>