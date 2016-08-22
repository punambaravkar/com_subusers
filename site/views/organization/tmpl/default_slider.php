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
jimport('joomla.filesystem.file');
jimport( 'joomla.filesystem.folder' );

$document =	JFactory::getDocument();
$document->addStylesheet(JUri::root(true).'/media/com_subusers/css/subusers.css');
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_subusers');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_subusers')) {
$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div id="myCarousel" class="carousel slide" data-ride="carousel">
	<?php
	$dir = JPATH_SITE . '/images/com_subusers/partner_slides/org_'.(int) $this->item->id;
	if(JFolder::exists($dir))
	{
		$filter = '.';
		//We only need images so exculding 'index.html' from folder
		$exclude = array('index.html', '.svn', 'CVS', '.txt', '.php');
		$files = JFolder::files($dir, $filter, false, false, $exclude );
		$fileroute = JUri::root(true) . '/images/com_subusers/partner_slides/org_'.(int) $this->item->id;
		?>
		<!-- Wrapper for slides -->
		<div class="carousel-inner" role="listbox">
			<?php if($files){ $j= count($files); ?>
				<!-- Indicators -->
				<ol class="carousel-indicators">
				<?php foreach ($files as $ind => $file) {  ?>
				<li data-target="#myCarousel" data-slide-to="<?php echo $ind; ?>" class="<?php echo ($ind == 0) ? 'active' : ' '?>"></li>
				<?php } ?>
				</ol>
			<?php foreach ($files as $ind => $file) : ?>
			 <div class="item <?php echo ($ind == 0) ? 'active' : ' '?>">
				 <?php echo '<img class="" src="' .$fileroute.  '/'. $file . ' "/> ' ;?>
			</div>
			<?php endforeach; } ?>
		</div>
		<!-- Left and right controls -->
		<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
		</a>

	<?php } else { 	} ?>
</div>
