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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_subusers');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_subusers')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

	<div class="item_fields">
		<table class="table">
			<tr>
			<th><?php echo JText::_('COM_SUBUSERS_FORM_LBL_ROLE_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SUBUSERS_FORM_LBL_ROLE_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SUBUSERS_FORM_LBL_ROLE_CLIENT'); ?></th>
			<td><?php echo $this->item->client; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SUBUSERS_FORM_LBL_ROLE_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SUBUSERS_FORM_LBL_ROLE_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>

		</table>
	</div>
	<?php if($canEdit && $this->item->checked_out == 0): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_subusers&task=role.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_SUBUSERS_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_subusers')):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_subusers&task=role.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_SUBUSERS_DELETE_ITEM"); ?></a>
								<?php endif; ?>
	<?php
else:
	echo JText::_('COM_SUBUSERS_ITEM_NOT_LOADED');
endif;
