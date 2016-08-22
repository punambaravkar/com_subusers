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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$document =	JFactory::getDocument();
$document->addStylesheet(JUri::root(true) . '/media/com_subusers/css/subusers.css');
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_subusers');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_subusers'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}

?>
			<?php if ($this->oluser_id)
			{
				if ($this->memberstate == '')
				{
					?>
					<form class="membership" id="membership"
					action="<?php echo JRoute::_('index.php?option=com_subusers&task=organization.applymembership'); ?>" method="post">
						<input type="hidden" name="option" value="com_subusers" />
						<input type="hidden" name="client_id" value="<?php echo $this->item->name; ?>" />
						<input type="hidden" value="organization.applymembership" name="task" />
						<input type="hidden" value="<?php echo $this->item->id; ?>" name="orgid" />
						<input type="button" onclick="member_confirm()" class="btn btn-primary" value="<?php echo JText::_('COM_SUBUSERS_APPLY_FOR_MEMBERSHIP'); ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
					<?php
				}
				elseif($this->memberstate == 0)
				{

				?>
					<div class="message-panel-box"><?php echo JText::_('COM_SUBUSERS_SEND_FOR_APPROVAL'); ?></div>
				<?php
				}
				elseif($this->memberstate == 1)
				{
				?>
					<div class="message-panel-box"><?php echo JText::_('COM_SUBUSERS_SAME_PARTNER_MEMBER'); ?></div>
				<?php
				}
			}
			else
			{
			?>
				<div class="message-panel-box"><?php echo JText::_('COM_SUBUSERS_LOGIN_FIRST'); ?></div>
				<?php
			}?>


<script type="text/javascript">
	function member_confirm(){
		var check = confirm("Are you sure you want to apply?");
		if(check == true)
		{
			jQuery('#membership').submit()
		}
	}
</script>
