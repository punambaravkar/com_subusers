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
jimport('joomla.application.module.helper');
JHTML::_('behavior.modal');

$document =	JFactory::getDocument();
$document->addStylesheet(JUri::root(true) . '/media/com_subusers/css/subusers.css');
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_subusers');
$pagetitle = $this->item->name;
JFactory::getDocument()->setTitle($pagetitle);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_subusers'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<?php
if ($this->item): ?>
<div class="tj-page">
	<?php if ($this->item):?>
		<div class="row">
			<!--<div class="col-xs-2 col-sm-2 col-md-1">
				<div>
					<?php foreach ((array) $this->item->logo as $singleFile) :
					if (!is_array($singleFile)):
					$uploadPath = 'images/com_subusers/partners/' . DIRECTORY_SEPARATOR . $singleFile;?>
						<img class="img-logo" id="dialogue-img" src="<?php echo JRoute::_(JUri::root() . $uploadPath, false) ?>" />
					<?php endif;
					endforeach;?>
				</div>
			</div>
			<div class="col-xs-8 col-sm-8 col-md-10">-->
			<div class="col-xs-12 col-sm-12 col-md-12">
				<h1 class="page-header"><?php echo $this->item->name; ?></h1>
			</div>
		</div>

		<div class="partner-slider"><?php echo $this->loadTemplate("slider");  ?></div>
		<div class="clearfix">&nbsp;</div>
		<div  class="partner-about module_blue">
			<span class="module-title"><?php echo JText::_('COM_SUBUSERS_ORGANISATION_ABOUT_US'); ?></span>
			<div class="partner-description">
				<?php echo $this->item->description;?>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	<?php endif; ?>

	<div class="row partner-related">
		<div class="col-xs-12 col-sm-12 col-md-4 pull-left">
			<?php if ($this->memberOrgId > 0 && !($this->oluser_id && $this->memberOrgId > 0 && $this->memberstate == 0)) {?>
			<div class="t3-module module_green"><div class="module-inner">
					<div class="module-title"><span><?php echo JText::_('COM_SUBUSERS_ORGANISATION_MEMBERS'); ?></span></div>
					<div class="memberlist">
						<?php

						if(($this->oluser_id && $this->memberOrgId <= 0))
						{
							?>
							<div><?php echo JText::_('COM_SUBUSERS_NOT_A_MEMBER'); ?></div>
							<?php
						}
						elseif(!($this->oluser_id && $this->memberOrgId > 0 && $this->memberstate == 0))
						{
							$module = JModuleHelper::getModule('mod_subusergroup', 'Sub Users Group');
							echo JModuleHelper::renderModule($module);
						}
						else
						{
						?>
							<div><?php echo JText::_('COM_SUBUSERS_NOT_A_MEMBER'); ?></div>
						<?php
						}
						?>
					</div>
			</div></div>
			<?php } ?>

			<?php if($this->memberstate != 1){ ?>
			<div class="t3-module module_green"><div class="module-inner">
					<div class="module-title"><span><?php echo JText::_('COM_SUBUSERS_ORGANISATION_APPLY'); ?></span></div>
					<div class="text-center">
					<?php echo $this->loadTemplate("membership");  ?>
					</div>
			</div></div>
			<?php } ?>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-8 pull-right">
			<div class="t3-module module_orange"><div class="module-inner">
				<div class="module-title"><span><?php echo JText::_('COM_SUBUSERS_ORGANISATION_ANNOUNCEMENTS'); ?></span></div>

				<?php if($this->checkIfOrgAdmin == 1)
				{ ?>
					<div class="org-add">
						<a href="<?php echo JRoute::_('index.php?option=com_subusers&task=announcementform.edit&id=0&org_id=' .  $this->item->id, false); ?>"
						class="btn btn-success btn-small add pull-right">
						<i class="fa fa-plus"></i>
						<?php echo JText::_('COM_SUBUSERS_ADD_ANNOUNCEMENT'); ?></a>
					</div>
				<?php } ?>

				<div class="module-ct">
					<?php
						$module = JModuleHelper::getModule('mod_announcements', 'Announcements');
						echo JModuleHelper::renderModule($module);
					?>
				</div>
			</div></div>

			<?php if ($this->memberOrgId > 0 && $this->memberstate == 1) {?>
			<div class="t3-module module_orange"><div class="module-inner">
					<div class="module-title"><span><?php echo JText::_('COM_SUBUSERS_PARTNER_ACTIONS'); ?></span></div>
					<div class="btn">
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=20');?>">
						<?php echo JText::_('COM_SUBUSERS_PARTNER_CHILD_TELEMETRY'); ?>
						</a>
						<br>
						<a href="<?php echo JRoute::_('index.php?option=com_ekcontent&view=dashboard&layout=operational');?>">
						<?php echo JText::_('COM_SUBUSERS_PARTNER_OPERATIONAL_DASHBOARD'); ?>
						</a>
					</div>
			</div></div>
			<?php } ?>
		</div>
	</div>
	<!-- Discussion module -->
		<!--<div class="col-xs-12 col-sm-12 col-md-8 pull-right">
			<?php
			$module = JModuleHelper::getModule('mod_partnerdiscussion', 'Sub Users Group');
			echo JModuleHelper::renderModule($module);
			?>
		</div> removed -->
	<!-- End of module-->

	<div class="clearfix">&nbsp;</div>
</div>

	<?php
else:
	echo JText::_('COM_SUBUSERS_ITEM_NOT_LOADED');
endif;
?>
