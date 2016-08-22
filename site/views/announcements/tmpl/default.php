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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$mainframe  = JFactory::getApplication();
$search_organisation = $mainframe->getUserStateFromRequest('.filter_organisation_search', 'filter_organisation_search');
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_subusers');
$canEdit    = $user->authorise('core.edit', 'com_subusers');
$canCheckin = $user->authorise('core.manage', 'com_subusers');
$canChange  = $user->authorise('core.edit.state', 'com_subusers');
$canDelete  = $user->authorise('core.delete', 'com_subusers');
?>

<script type="text/javascript">
jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_announcement_search').val('');
			jQuery('#adminForm').submit();
		});
});
</script>

<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		<?php if($canDelete) : ?>
		if (confirm("<?php echo JText::_('COM_SUBUSERS_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_subusers&task=announcementform.remove&id=', false, 2) ?>' + item_id;
		}
		<?php endif; ?>
	}
</script>

<div class="tj-page">
	<div class="row">
		<div class="col-xs-12 col-md-6 col-sm-6">
			<h1><?php echo JText::_('COM_SUBUSERS_ANNOUNCEMENTS'); ?></h1>
		</div>

		<div class="col-xs-12 col-md-6 col-sm-6 org-add">
			<?php if ($canCreate) : ?>
				<a href="<?php echo JRoute::_('index.php?option=com_subusers&task=announcementform.edit&id=0', false, 2); ?>"
				class="btn btn-success btn-small pull-right">
				<i class="icon-plus"></i>
				<?php echo JText::_('COM_SUBUSERS_ADD_ANNOUNCEMENT'); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_subusers&view=announcements'); ?>" method="post"
		  name="adminForm" id="adminForm">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible">
						<?php echo JText::_('JSEARCH_FILTER');?>
					</label>
					<input type="text" name="filter_announcement_search"
							id="filter_announcement_search"
							placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
							value="<?php //echo $search_announcement;?>"
							title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
				</div>

				<div class="pull-left">
					<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="fa fa-search"></i>
					</button>
					<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
						<i class="fa fa-times"></i>
					</button>
				</div>

				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible">
						<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			</div>

			<?php // check  if record is present or not
				if (!empty($this->items))
				{
			?>
			<div class="table-responsive tj-page">
				<table class="table table-striped" id="announcementList">
					<thead>
					<tr>
						<?php if (isset($this->items[0]->id)): ?>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>

						<?php if (isset($this->items[0]->state)): ?>
							<th width="5%">
								<?php echo JHtml::_('grid.sort', 'COM_SUBUSERS_ORGANISATIONS_STATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>

						<th width="20%">
							<?php echo JHtml::_('grid.sort',  'COM_SUBUSERS_ANNOUNCEMENT_TITLE', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th width="60%">
							<?php echo JHtml::_('grid.sort',  'COM_SUBUSERS_ANNOUNCEMENT_DESCRIPTION', 'a.email', $listDirn, $listOrder); ?>
						</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<?php $canEdit = $user->authorise('core.edit', 'com_subusers'); ?>

						<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_subusers')): ?>
							<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
						<?php endif; ?>

						<tr class="row<?php echo $i % 2; ?>">
								<?php if (isset($this->items[0]->id)): ?>
									<td class="center hidden-phone">
										<?php echo (int) $item->id; ?>
									</td>
								<?php endif; ?>

								<?php if (isset($this->items[0]->state)) : ?>
									<?php $class = ($canEdit || $canChange) ? 'active' : 'disabled'; ?>
									<td class="center">
										<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canEdit || $canChange) ? JRoute::_('index.php?option=com_subusers&task=announcement.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
											<?php if ($item->state == 1): ?>
												<i class="fa fa-check"></i>
											<?php else: ?>
												<i class="fa fa-times"></i>
											<?php endif; ?>
										</a>
									</td>
								<?php endif; ?>
							</td>

							<td>
								<?php if ($canEdit): ?>
									<a href="<?php echo JRoute::_('index.php?option=com_subusers&task=announcementform.edit&id=' . $item->id, false, 2); ?>">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php else: ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif;?>
							</td>
							<td>
								<?php echo $item->introtext; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php } else { ?>
			<div align="center"><?php echo JText::_('COM_SUBUSERS_NO_ANNOUNCEMENT_FOUND');?></div>
		<?php }  ?>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
