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

// Create object
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_subusers/js/sub.js');

// Sweet alerts
$doc->addScript(JUri::root(true) . '/media/com_ekcontent/vendors/sweetalert/sweetalert.min.js');
$doc->addStyleSheet(JUri::root(true) . '/media/com_ekcontent/vendors/sweetalert/sweetalert.css');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_subusers', JPATH_SITE);

$doc->addScript(JUri::base() . '/media/com_subusers/js/form.js');
$doc->addStylesheet(JUri::root(true) . '/media/com_subusers/css/subusers.css');
$user = JFactory::getUser();
$userId = $user->get( 'id' );

// Acl helper call
require_once JPATH_COMPONENT . '/helpers/acl.php';

// Call static method for language constant
SubusersFrontendHelper::getLanguageConstant();
$orgId = $this->item->id;
$userdata = SubusersAclHelper::partnerAdminName($orgId);
?>

<script type="text/javascript">
	var isEdit = <?php if (!empty($this->item->id)) { echo '1'; } else { echo '0'; }?>;
	jQuery(document).on('change', '.btn-file :file', function() {
    var input = jQuery(this),
    numFiles = input.get(0).files ? input.get(0).files.length : 1,
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
	});

	jQuery(document).ready( function() {
    jQuery('.btn-file :file').on('fileselect', function(event, numFiles, label) {

        var input = jQuery(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }

		if(!html5FileCheck('jform_logo')){
			jQuery('#jform_logo').val('');
			jQuery('#file_upload_dummy_text').val('');
		}
    });
});

jQuery(document).ready(function () {
		jQuery('#form-organization').submit(function (event) {
			if(jQuery('#jform_logo').val() != ''){
				jQuery('#jform_logo_hidden').val(jQuery('#jform_logo').val());
			}
			if (jQuery('#jform_logo').val() == '' && jQuery('#jform_logo_hidden').val() == '') {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
				event.preventDefautl();
			}
		});
		<?php if (!empty($this->item->logo)) : ?>
			jQuery('#jform_logo').removeAttr("required");
			jQuery('#jform_logo').removeClass("required");
		<?php endif; ?>
	});


	var tjAllowedMediaSize = '<?php echo $this->subusersParams->get('max_logo_size') * 1024 * 1024; ?>';
	var tjAllowedMimeTypes = [
		'image/jpeg',
		'image/pjpeg',
		'image/jpeg',
		'image/pjpeg',
		'image/jpeg',
		'image/pjpeg',
		'image/png'
	];

	/*+manoj*/
	function html5FileCheck(fieldId) {
		/*Check for browser support for all File API*/
		if(window.File && window.FileReader && window.FileList && window.Blob) {
			/*Get file size and file type*/
			var fsize = jQuery('#' + fieldId)[0].files[0].size;
			var ftype = jQuery('#' + fieldId)[0].files[0].type;
			/*Check file size*/
			if(fsize > tjAllowedMediaSize) {
				swal(Joomla.JText._('COM_SUBUSER_MSG_ERR'), Joomla.JText._('COM_SUBUSERS_ERR_MSG_JS_FILE_SIZE'), "error");
				return false;
			}
			/*Check mime type*/
			if(jQuery.inArray(ftype, tjAllowedMimeTypes) == -1) {
				swal(Joomla.JText._('COM_SUBUSER_MSG_ERR'), Joomla.JText._('COM_SUBUSERS_ERR_MSG_JS_FILE_TYPES'), "error");
				return false;
			}
			return true;
		}
	}
</script>
<script type="text/javascript">
	sub.organizationform.initOrganizationformJs();
</script>

<div class="organization-edit front-end-edit tj-page">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
			<?php if (!empty($this->item->id)): ?>
				<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_EDIT_ORGANISATION'); ?></h1>
			<?php else: ?>
				<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_ADD_ORGANISATION'); ?></h1>
			<?php endif; ?>
		</div>
	</div>

	<form id="form-organization"
		action="<?php echo JRoute::_('index.php?option=com_subusers&task=organization.save'); ?>"
		method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label">
						<?php echo $this->form->getLabel('id'); ?>
					</label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
							<?php echo $this->form->getInput('id'); ?>
						</div>
				</div>

				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label">
						<?php echo $this->form->getLabel('name'); ?>
					</label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
							<?php echo $this->form->getInput('name'); ?></div>
						</div>

				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label">
						<?php echo $this->form->getLabel('email'); ?>
					</label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
							<?php echo $this->form->getInput('email'); ?>
						</div>
				</div>

				<?php if (!empty($userdata))
				{
				?>
					<div class="form-group" id="org-admin">
						<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label">
							<?php echo JText::_('COM_SUBUSERS_ORGANIZATION_ADMIN'); ?>
						</label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
							<?php echo $userdata->name; ?>
						</div>
					</div>
				<input type="hidden" name="jform[userid]" value="<?php echo $userdata->id; ?>" />
				<?php  }
				else
				{
				?>
					<div class="form-group" id="org-admin">
						<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label">
							<?php echo $this->form->getLabel('userid'); ?>
						</label>
						<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
							<?php echo $this->form->getInput('userid'); ?>
						</div>
					</div>
			<?php } ?>


				<div class="form-group">
					<?php echo $this->form->getLabel('logo'); ?>
						<div class="<?php echo $this->form->getFieldAttribute('logo', 'inputclass'); ?>">
							<div class="input-group">
								<span class="input-group-btn">
									<span class="btn btn-primary btn-file">
										<?php echo JText::_('COM_SUBUSERS_BTN_BROWSE'); ?>
										<?php echo $this->form->getInput('logo'); ?>
									</span>
								</span>
								<input id="file_upload_dummy_text" type="text" class="file_upload_dummy_text" size="28" readonly>
							</div>

							<p class="help-block small">
									<?php echo JText::sprintf('COM_SUBUSERS_MEDIA_LOGO_TYPES', $this->subusersParams->get('allowed_logo_types')); ?>
									<br/>
								<?php echo JText::sprintf('COM_SUBUSERS_MEDIA_MAX_ALLOWED_SIZE', $this->subusersParams->get('max_logo_size')); ?>
							</p>

							<div>
							<?php if (!empty($this->item->logo)) :
								foreach ((array) $this->item->logo as $singleFile) :
									if (!is_array($singleFile)) :
										echo '<img class="ek-img-200 thumbnail" src="' . JRoute::_(JUri::root() . 'images/com_subusers/partners/' . $singleFile, false) . '"/>';
									endif;
								endforeach;
							endif;?>
							 </div>

						<input type="hidden" name="jform[logo][]" id="jform_logo_hidden" value="<?php echo str_replace('Array,', '', implode(',', (array) $this->item->logo)); ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label">
						<?php echo $this->form->getLabel('description'); ?>
					</label>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
						<?php echo $this->form->getInput('description'); ?>
					</div>
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
											href="<?php echo JRoute::_('index.php?option=com_subusers&task=organizationform.cancel'); ?>"
											title="<?php echo JText::_('JCANCEL'); ?>">
											<?php echo JText::_('JCANCEL'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>

					<input type="hidden" name="jform[created_by]" value="<?php echo $userId; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<input type="hidden" name="jform[logo][]" id="jform_logo_hidden" value="<?php echo str_replace('Array,', '', implode(',', (array) $this->item->logo)); ?>" />
					<input type="hidden" name="option" value="com_subusers"/>
					<input type="hidden" name="task"
					value="organizationform.save"/>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>


