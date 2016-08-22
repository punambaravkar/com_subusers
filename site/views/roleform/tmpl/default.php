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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_subusers', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_subusers/js/form.js');

/**/
?>
<script type="text/javascript">
	if (jQuery === 'undefined') {
		document.addEventListener("DOMContentLoaded", function (event) {
			jQuery('#form-role').submit(function (event) {

			});
		});
	} else {
		jQuery(document).ready(function () {
			jQuery('#form-role').submit(function (event) {

			});
		});
	}
</script>

<div class="role-edit front-end-edit">
	<?php if (!empty($this->item->id)): ?>
		<h1><?php echo JText::_('COM_SUBUSERS_ROLES_EDIT'); ?> <?php echo $this->item->id; ?></h1>
	<?php else: ?>
		<h1><?php echo JText::_('COM_SUBUSERS_ROLES_ADD'); ?></h1>
	<?php endif; ?>

	<form id="form-role"
		  action="<?php echo JRoute::_('index.php?option=com_subusers&task=role.save'); ?>"
		  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('name'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="form-group">
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12 pull-right">

						<?php if ($this->canSave): ?>
							<button type="submit" class="validate btn btn-primary">
								<?php echo JText::_('JSUBMIT'); ?>
							</button>
						<?php endif; ?>
						<a class="btn"
						   href="<?php echo JRoute::_('index.php?option=com_subusers&task=roleform.cancel'); ?>"
						   title="<?php echo JText::_('JCANCEL'); ?>">
							<?php echo JText::_('JCANCEL'); ?>
						</a>
					</div>
				</div>
				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[client]" value="<?php echo $this->item->client; ?>" />

				<?php if(empty($this->item->created_by)): ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />
				<?php else: ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
				<?php endif; ?>
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="option" value="com_subusers"/>
				<input type="hidden" name="task"
					   value="roleform.save"/>
				<?php echo JHtml::_('form.token'); ?>
		</div></div>
	</form>
</div>
