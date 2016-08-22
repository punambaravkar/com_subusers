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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
jimport( 'joomla.html.html.select' );

// Load js
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_subusers/js/sub.js');

// User srach by organisation
$mainframe                = JFactory::getApplication();
$search_organisationuser = $mainframe->getUserStateFromRequest('.filter_user_search', 'filter_user_search');

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
	/*Js for clear search text button*/
	sub.users.initUsersJs();
</script>

<div class="tj-page">
	<div class ="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<h1 class="page-header"><?php echo JText::_('COM_SUBUSERS_USERS'); ?></h1>
		</div>
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_subusers&view=users'); ?>" method="post"
		  name="adminForm" id="adminForm">

		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible">
					<?php echo JText::_('JSEARCH_FILTER');?>
				</label>

				<input class="pull-left input-medium" type="text" name="filter_user_search" id="filter_user_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $search_organisationuser;?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />

				<div class="pull-left">
					<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="fa fa-search"></i>
					</button>

					<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
						<i class="fa fa-times"></i>
					</button>
				</div>
			</div>

			<?php if($this->isSuperAdmin)
			{?>
				<div class="btn-group pull-left">
					<?php echo JHTML::_('select.genericlist', $this->orgListArray,'filter_org_search', 'class="inputbox" onChange="jQuery(\'#adminForm\').submit();"', 'value', 'text', $this->orgsearch, $disable = false);?>
				</div>
			<?php }?>

			<div class="btn-group pull-right">
				<!-- If looged User is Super Admin -->
				<?php if($this->isSuperAdmin || $this->isAdmin)
				{
					if ($canCreate) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_subusers&task=userform.edit&id=0', false); ?>"
						class="btn btn-success btn-small  pull-right"><i
							class="icon-plus"></i>
						<?php echo JText::_('COM_SUBUSERS_ADD_USER'); ?></a>
					<?php endif;
				} ?>
			</div>

			<div class="btn-group pull-right hidden-xs">
				<label for="limit" class="element-invisible">
					<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
				</label>
				<?php echo $this->pagination->getLimitBox();?>
			</div>
		</div>


		<?php // Check if record is present
		if ($this->items)
		{ ?>
			<table class="table table-striped" id="userList">
				<thead>
					<tr>
						<?php if (isset($this->items[0]->id)): ?>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>

						<?php if (isset($this->items[0]->state)): ?>
							<th width="5%">
								<?php if ($this->isSuperAdmin || $this->isAdmin)
								{?>
									<?php echo JHtml::_('grid.sort', 'COM_SUBUSERS_USERS_STATUS', 'a.state', $listDirn, $listOrder); ?>
								<?php }?>
							</th>

						<?php endif; ?>


						<th class=''>
							<?php echo JHtml::_('grid.sort',  'COM_SUBUSERS_USERS_USER_NAME', 'uc.name', $listDirn, $listOrder); ?>
						</th>

						<th class=''>
							<?php echo JHtml::_('grid.sort',  'COM_SUBUSERS_USERS_USER_EMAIL', 'uc.email', $listDirn, $listOrder); ?>
						</th>

						<th class=''>
								<?php echo JHtml::_('grid.sort',  'COM_SUBUSERS_USERS_ORGANIZATION_NAME','orgname', $listDirn, $listOrder); ?>
						</th>

						<th class=''>
							<?php echo JHtml::_('grid.sort',  'COM_SUBUSERS_USERS_ROLE_ID', 'a.role_id', $listDirn, $listOrder); ?>
						</th>

						<?php if ($canEdit || $canDelete): ?>
							<th class="center">
								<?php if ($this->isSuperAdmin || $this->isAdmin)
								{?>
									<?php echo JText::_('COM_SUBUSERS_USERS_ACTIONS'); ?>
								<?php }?>
							</th>
						<?php endif; ?>
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
										<?php if ($this->isSuperAdmin || $this->isAdmin)
										{?>
											<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canEdit || $canChange) ? JRoute::_('index.php?option=com_subusers&task=user.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false) : '#'; ?>">
												<?php if ($item->state == 1): ?>
													<i class="fa fa-check"></i>
												<?php else: ?>
													<i class="fa fa-close"></i>
												<?php endif; ?>
											</a>
										 <?php }?>
									</td>
								<?php endif; ?>

								<td>
									<a href="">
										<?php echo JFactory::getUser($item->user_id)->name; ?>
									</a>
								</td>

								<td>
									<?php echo JFactory::getUser($item->user_id)->email; ?>
								</td>

								<td>
										<?php echo $item->orgname; ?>
								</td>

								<td>
									<?php echo $item->name; ?>
								</td>

								<?php if ($canEdit || $canDelete): ?>
									<td class="center">
										<?php if ($canEdit): ?>
											<!-- <a href="<?php echo JRoute::_('index.php?option=com_subusers&task=userform.edit&id=' . $item->id, false); ?>" class="btn btn-mini" type="button"><i class="fa fa-edit" ></i></a> -->
										<?php endif; ?>

										<?php if ($this->isSuperAdmin || $this->isAdmin)
										{?>
											<?php
											if ($canDelete):
												if ($item->name != "Organisation Admin") : ?>
													<button data-item-id="<?php echo $item->id; ?>" class="btn btn-mini delete-button" type="button"><i class="fa fa-trash" ></i></button>
												<?php endif; ?>
											<?php endif;
										}?>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php
		} else { ?>
			<div align="center"><?php echo JText::_('COM_SUBUSERS_NO_USER_FOUND');?></div>
		<?php }  ?>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		<?php if($canDelete) : ?>
		if (confirm("<?php echo JText::_('COM_SUBUSERS_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_subusers&task=userform.remove&id=', false) ?>' + item_id;
		}
		<?php endif; ?>
	}
</script>


