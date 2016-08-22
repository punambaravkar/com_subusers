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

$mainframe           = JFactory::getApplication();
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

$document = JFactory::getDocument();
$document->addStylesheet(JUri::root(true) . '/media/com_subusers/css/subusers.css');
?>

<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('#clear-search-button').on('click', function () {
		jQuery('#filter_organisation_search').val('');
		jQuery('#adminForm').submit();
	});
});
</script>

<div class="tj-page partners">
	<div class="row">
		<div class="col-xs-12 col-md-12 col-lg-12">
			<form action="<?php echo JRoute::_('index.php?option=com_subusers&view=organizations&layout=pin'); ?>" method="get"
				  name="adminForm" id="adminForm">

				<div id="filter-bar" class="btn-toolbar">
							<div class="filter-search btn-group pull-left">
								<label for="filter_search" class="element-invisible">
									<?php echo JText::_('JSEARCH_FILTER');?>
								</label>
								<input type="text" class="pull-left input-medium" name="filter_organisation_search" id="filter_organisation_search"
									placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
									value="<?php echo $search_organisation;?>"
									title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
							</div>
							<div class="pull-right">
								<button class="btn btn-primary hasTooltip" type="submit"
									title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
									<i class="icon-search fa fa-search"></i><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
								</button>
								<!--<button class="btn hasTooltip" id="clear-search-button" type="button"
									title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
									<i class="fa fa-times"></i>
								</button>-->
							</div>							
							<div class="btn-group pull-right hidden-phone hidden-xs">
								<label for="limit" class="element-invisible">
									<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
								</label>
								<?php echo $this->pagination->getLimitBox(); ?>
							</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-md-12 col-lg-12">
						<h1 class="page-header">
							<?php echo JText::_('COM_SUBUSERS_ORGANISATIONS'); ?>
						</h1>
					</div>
				</div>

				<div class="row">
					<?php
					// Check if record is present
					if ($this->items)
					{
						foreach ($this->items as $i => $item)
						{
							?>
							<!-- Pin Start-->
							<div class="pin col-xs-12 col-sm-6 col-md-4 col-lg-3 partners">
								<div class="thumbnail">
									<a href="<?php echo JRoute::_('index.php?option=com_subusers&view=organization&id=' . (int) $item->id, false); ?>">
										<?php
										if ($item->logo)
										{
											$imgUrl = JUri::root() . 'images/com_subusers/partners/' . $item->logo;
										?>
											<div style="background:linear-gradient( transparent,transparent,#333 ),url('<?php echo $imgUrl; ?>'); background-size:cover; background-position:top center;background-image:-webkit-linear-gradient( transparent,transparent,#333 ),url('<?php echo $imgUrl; ?>');" alt="<?php echo $this->escape($item->name); ?>
											"></div>

										<?php
										}
										else
										{
										 ?>
											<div style="background:linear-gradient( transparent,transparent,#333 ); background-size:cover;background-image:-webkit-linear-gradient( transparent,transparent,#333 ),url('http://placehold.it/200x200'); " alt="<?php echo $this->escape($item->name); ?>"></div>
										 <?php
										}
										?>
									</a>

									<div class="caption">
										<h4>
											<a href="<?php echo JRoute::_('index.php?option=com_subusers&view=organization&id=' . (int) $item->id, false); ?>">
												<?php echo $this->escape($item->name); ?>
											</a>
										</h4>

										<p class="description"><?php echo JHtml::_('string.truncate', ($item->description), 100);?></p>
									</div>
								</div>
							</div>
							<!-- Pin End-->
							<?php
						}
					}
					else
					{
						?>
						<div class="col-xs-12 col-sm-12 col-md-12">
							<div class="center">
								<?php echo JText::_('COM_SUBUSERS_NO_ORGANISATION_FOUND');?>
							</div>
						</div>
						<?php
					}
					?>
				</div>

				<div class="clearfix">&nbsp;</div>

				<input type="hidden" name="layout" value="pin"/>
				<!--
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="filter_order" value="<?php // echo $listOrder; ?>"/>
				<input type="hidden" name="filter_order_Dir" value="<?php // echo $listDirn; ?>"/>
				-->

				<?php // echo JHtml::_('form.token'); ?>

				<div class="page-footer"><?php echo $this->pagination->getPagesLinks(); ?></div>
			</form>
		</div>
	</div>
</div>
