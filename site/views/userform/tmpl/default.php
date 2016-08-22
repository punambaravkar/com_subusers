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

// Call static method for laguage constatnt
SubusersFrontendHelper::getLanguageConstant();

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_subusers', JPATH_SITE);

$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_subusers/js/form.js');
$doc->addScript(JUri::base() . '/media/com_subusers/js/sub.js');


// Sweet alerts
$doc->addScript(JUri::root(true) . '/media/com_ekcontent/vendors/sweetalert/sweetalert.min.js');
$doc->addStyleSheet(JUri::root(true) . '/media/com_ekcontent/vendors/sweetalert/sweetalert.css');
$doc->addStylesheet(JUri::root(true) . '/media/com_subusers/vendors/bootstrap-checkbox-radio/neoelemento.css');

// Acl helper call
$achelperPath = JPATH_SITE.'/components/com_subusers/helpers/acl.php';
$user = JFactory::getUser();

if (!class_exists('SubusersAclHelper'))
{
	//require_once $path;
	JLoader::register('SubusersAclHelper', $achelperPath );
	JLoader::load('SubusersAclHelper');
}
$achelperPath = new SubusersAclHelper;
$userdata = $achelperPath->isOrganisationAdmin($user->id);
?>

<script type="text/javascript">
	sub.userform.initUserFormJs();
</script>

<div class="tj-page">
	<div class="row">
		<div class="user-edit front-end-edit col-xs-12 col-sm-12 col-md-12">
			<?php if (!empty($this->item->id)): ?>
			<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_EDIT_USER'); ?></h1>
			<?php else: ?>
			<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_ADD_USER'); ?></h1>
			<?php endif; ?>
		</div>
	</div>

	<form id="form-user"
		  action="<?php echo JRoute::_('index.php?option=com_subusers&task=user.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

			<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('name'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('name'); ?></div>
				</div>

				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('username'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('username'); ?></div>
				</div>

				<?php if (!empty($userdata))
				{
					?>
					<div class="form-group">
						<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo JText::_('COM_SUBUSERS_ORGANISATIONS'); ?></label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $userdata->name;?></div>
					</div>
					<input type="hidden" name="jform[client_id]" value="<?php echo $userdata->client_id; ?>" />
				<?php  }
				else
				{
					?>
					<div class="form-group">
						<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('client_id'); ?></label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('client_id'); ?></div>
					</div>
				<?php } ?>

				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('role_id'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('role_id'); ?></div>
				</div>


				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label"><?php echo $this->form->getLabel('email'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><?php echo $this->form->getInput('email'); ?></div>
				</div>

				<div class="form-group">
					<label id="autopass-lbl" class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label hasTooltip"  data-original-title="<strong>Autopass</strong><br />">Autopass</label>
					<div class="checkbox col-lg-10 col-md-10 col-sm-9 col-xs-12 "><label><input type="checkbox" name="autopass" id="autopass" value="0" default="0" /><span></span></label></div>
				</div>

				<div class="form-group">
					<label id="password-lbl" class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label hasTooltip"  data-original-title="<strong><?php echo JText::_('COM_SUBUSERS_USER_ENTER_PASSWORD'); ?></strong><br />"><?php echo JText::_('COM_SUBUSERS_USER_PASSWORD'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><input type="password" name="jform[password]" id="password" /></div>
				</div>

				<div class="form-group">
					<label id="repeat-password-lbl" class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label hasTooltip"  data-original-title="<strong><?php echo JText::_('COM_SUBUSERS_USER_ENTER_REPEAT_PASSWORD'); ?></strong><br />"><?php echo JText::_('COM_SUBUSERS_USER_REPEAT_PASSWORD'); ?></label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12"><input type="password" name="jform[repeat-password]" id="repeat-password"  /></div>
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
								   href="<?php echo JRoute::_('index.php?option=com_subusers&task=userform.cancel'); ?>"
								   title="<?php echo JText::_('JCANCEL'); ?>">
									<?php echo JText::_('JCANCEL'); ?>
								</a>
							</div>
						</div>
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
					   value="userform.save"/>
				<?php echo JHtml::_('form.token'); ?>
		</div></div>
	</form>
</div>
