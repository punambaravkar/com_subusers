<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JPATH_ROOT . 'media/com_subusers/css/edit.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
	});
	Joomla.submitbutton = function (task) {
		if (task == 'organization.cancel') {
			Joomla.submitform(task, document.getElementById('organization-form'));
		}
		else {
			js = jQuery.noConflict();
			if(js('#jform_logo').val() != ''){
				js('#jform_logo_hidden').val(js('#jform_logo').val());
			}
			if (js('#jform_logo').val() == '' && js('#jform_logo_hidden').val() == '') {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
				return;
			}
			if (task != 'organization.cancel' && document.formvalidator.isValid(document.id('organization-form'))) {
				Joomla.submitform(task, document.getElementById('organization-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_subusers&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="organization-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_SUBUSERS_TITLE_ORGANIZATION', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
					</div>

					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
					</div>

					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
					</div>

					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('logo'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('logo'); ?></div>
					</div>

					<?php if (!empty($this->item->logo)) : ?>
						<?php foreach ((array)$this->item->logo as $fileSingle) : ?>
							<?php if (!is_array($fileSingle)) : ?>
								<a href="<?php echo JRoute::_(JUri::root() . 'images/com_subusers/partners/' . DIRECTORY_SEPARATOR . $fileSingle, false);?>"><?php echo $fileSingle; ?></a> |
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<input type="hidden" name="jform[logo][]" id="jform_logo_hidden" value="<?php echo implode(',', (array)$this->item->logo); ?>" />
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
