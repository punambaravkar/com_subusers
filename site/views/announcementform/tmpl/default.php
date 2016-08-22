<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015 Techjoomla. All rights reserved.
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
$user = JFactory::getUser();
$userId = $user->get('id');

?>

<script type="text/javascript">
	if (jQuery === 'undefined') {
		document.addEventListener("DOMContentLoaded", function (event) {
			jQuery('#form-announcement').submit(function (event) {
			});
		});
	} else {
		jQuery(document).ready(function () {
			jQuery('#form-announcement').submit(function (event) {
			});
		});
	}
</script>

<div class="organization-edit front-end-edit tj-page">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
			<?php if (!empty($this->item->id)): ?>
				<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_EDIT_ANNOUNCEMENT'); ?></h1>
			<?php else: ?>
				<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_ADD_ANNOUNCEMENT'); ?></h1>
			<?php endif; ?>
		</div>
	</div>

	<form id="form-announcement"
		  action="<?php echo JRoute::_('index.php?option=com_subusers&task=announcement.save'); ?>"
		  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('title'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('title'); ?></div>
				</div>

				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('introtext'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('introtext'); ?></div>
				</div>


				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<hr/>
						<div class="form-group">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
								<?php if ($this->canSave): ?>
									<button type="submit" class="validate btn btn-primary">
										<?php echo JText::_('JSUBMIT'); ?>
									</button>
								<?php endif; ?>
								<a class="btn btn-default"
								   href="<?php echo JRoute::_('index.php?option=com_subusers&task=announcementform.cancel&orgid=' . $this->org_id); ?>"
								   title="<?php echo JText::_('JCANCEL'); ?>">
									<?php echo JText::_('JCANCEL'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>

				<input type="hidden" name="jform[orgid]" value="<?php echo $this->org_id; ?>" />
				<input type="hidden" name="jform[created_by]" value="<?php echo $userId; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="option" value="com_subusers"/>
				<input type="hidden" name="task"
					   value="announcementform.save"/>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>
