<?php##{start_header}##
/**
 * @package     JDeveloper
 * @subpackage  Templates.Component
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;##{end_header}##
##Header##

// necessary libraries
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

// sort ordering and direction
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
##{start_published}##$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
##{end_published}##?>
<style>
.row2 {
	background-color: #e4e4e4;
}
</style>

<h2><?php echo JText::_('COM_##COMPONENT##_##TABLE##_VIEW_##PLURAL##_TITLE'); ?></h2>
<form action="<?php JRoute::_('index.php?option=com_mythings&view=mythings'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="category table table-striped table-bordered table-hover">	
		<thead>
			<tr>				
				<th id="itemlist_header_title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.##mainfield##', $listDirn, $listOrder); ?>
				</th>##tablehead####{start_created_by}##
				<?php if ($this->params->get('list_show_author', 1)) : ?>
				<th id="itemlist_header_author">
					<?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>##{end_created_by}####{start_hits}##
				<?php if ($this->params->get('list_show_hits', 1)) : ?>
				<th id="itemlist_header_hits">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>##{end_hits}##
				<?php if ($this->user->authorise('core.edit') || $this->user->authorise('core.edit.own')) : ?>
				<th id="itemlist_header_edit"><?php echo JText::_('COM_##COMPONENT##_EDIT_ITEM'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>		
		<tbody>
		<?php foreach ($this->items as $i => $item) :
		$canEdit	= $this->user->authorise('core.edit',       'com_##component##'##{start_asset_id}##.'.##singular##.'.$item->##pk####{end_asset_id}##);
		$canEditOwn	= $this->user->authorise('core.edit.own',   'com_##component##'##{start_asset_id}##.'.##singular##.'.$item->##pk####{end_asset_id}##)##{start_created_by}## && $item->created_by == $this->user->id##{end_created_by}##;
		$canDelete	= $this->user->authorise('core.delete',       'com_##component##'##{start_asset_id}##.'.##singular##.'.$item->##pk####{end_asset_id}##);
		$canCheckin	= $this->user->authorise('core.manage',     'com_checkin')##{start_checked_out}## || $item->checked_out == $this->user->id || $item->checked_out == 0##{end_checked_out}##;
		$canChange	= $this->user->authorise('core.edit.state', 'com_##component##'##{start_asset_id}##.'.##singular##.'.$item->##pk####{end_asset_id}##) && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">				
				<td headers="itemlist_header_title" class="list-title">##{start_table_nested}##
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>##{end_table_nested}##
					<?php if (isset($item->access) && in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
						<a href="<?php echo JRoute::_("index.php?option=com_##component##&view=##singular##&##pk##=" . $item->##pk##); ?>">
							<?php echo $this->escape($item->##mainfield##); ?>
						</a>
					<?php else: ?>
						<?php echo $this->escape($item->##mainfield##); ?>
					<?php endif; ?>##{start_published}##
					<?php if ($item->published == 0) : ?>
						<span class="list-published label label-warning">
							<?php echo JText::_('JUNPUBLISHED'); ?>
						</span>
					<?php endif; ?>##{end_published}####{start_publish_up}##
					<?php if (strtotime($item->publish_up) > strtotime(JFactory::getDate())) : ?>
						<span class="list-published label label-warning">
							<?php echo JText::_('JNOTPUBLISHEDYET'); ?>
						</span>
					<?php endif; ?>##{end_publish_up}####{start_publish_down}##
					<?php if ((strtotime($item->publish_down) < strtotime(JFactory::getDate())) && $item->publish_down != '0000-00-00 00:00:00') : ?>
						<span class="list-published label label-warning">
							<?php echo JText::_('JEXPIRED'); ?>
						</span>
					<?php endif; ?>##{end_publish_down}##
				</td>##tablebody####{start_created_by}##
				<?php if ($this->params->get('list_show_author', 1)) : ?>
				<td headers="itemlist_header_author" class="list-author">
					<?php if (!empty($item->author)##{start_created_by_alias}## || !empty($item->created_by_alias)##{end_created_by_alias}##) : ?>
						<?php $author = $item->author ?>##{start_created_by_alias}##
						<?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>##{end_created_by_alias}##
						<?php echo $author; ?>
					<?php endif; ?>
				</td>
				<?php endif; ?>##{end_created_by}####{start_hits}##
				<?php if ($this->params->get('list_show_hits', 1)) : ?>
				<td headers="itemlist_header_hits" class="list-hits">
					<span class="badge badge-info">
						<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
					</span>
				</td>
				<?php endif; ?>##{end_hits}##
				<?php if ($this->user->authorise('core.edit') || $this->user->authorise('core.edit.own')) : ?>
				<td headers="itemlist_header_edit" class="list-edit">
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo JRoute::_("index.php?option=com_##component##&task=##singular##.edit&##pk##=" . $item->##pk##); ?>"><i class="icon-edit"></i> <?php echo JText::_("JGLOBAL_EDIT"); ?></a>
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>
		<?php endforeach ?>
		</tbody>		
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>	
	</table>
	<div>
		<input type="hidden" name="task" value=" " />
		<input type="hidden" name="boxchecked" value="0" />
		<!-- Sortierkriterien -->
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>