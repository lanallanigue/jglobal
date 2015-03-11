<?php
/**
 * @package     JDeveloper
 * @subpackage  Views
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$input = JFactory::getApplication()->input;

?>
<form action="<?php echo JRoute::_('index.php?option=com_jdeveloper&view=plugin'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<?php if (!empty($this->item->id)) : ?>
		<h2 style="font-size:26px;"><?php echo $this->item->display_name ?></h2>
		<?php echo $this->loadTemplate("toolbar") ?>
		<p>&nbsp;</p>
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_JDEVELOPER_PLUGIN'), true); ?>
					<?php echo $this->loadTemplate("info") ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'overrides', JText::_('COM_JDEVELOPER_PLUGIN_OVERRIDES'), false); ?>
					<?php echo $this->loadTemplate("overrides") ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<?php else : ?>
		<h2><?php echo JText::_("COM_JDEVELOPER_PLUGIN") ?></h2>
		<div class="alert alert-info">
			<?php echo JText::_("COM_JDEVELOPER_PLUGIN_NO_PLUGIN_SELECTED"); ?>
		</div>
		<button data-toggle="modal" data-target="#switchItem" class="btn btn-info"><i class="icon-list"></i> <?php echo JText::_("COM_JDEVELOPER_PLUGIN_SWITCH"); ?></button>
	<?php endif; ?>
		<div class="modal hide fade" id="switchItem" style="width:700px;">
			<div class="modal-header">
				<h3><?php echo JText::sprintf('COM_JDEVELOPER_PLUGIN_SWITCH');?></h3>
			</div>
			<div class="modal-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<td><?php echo JText::_("COM_JDEVELOPER_PLUGINS"); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->items as $plugin) : ?>
						<tr>
							<td><a href="<?php echo JRoute::_("index.php?option=com_jdeveloper&view=plugin&id=" . $plugin->id, false); ?>"><?php echo $plugin->display_name; ?></a></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn" type="button" data-dismiss="modal">
					<?php echo JText::_('JTOOLBAR_CLOSE'); ?>
				</button>
			</div>
		</div>
		<div class="modal hide fade" id="deleteItem" style="width:400px;">
			<div class="modal-header">
				<h3><?php echo JText::sprintf('COM_JDEVELOPER_PLUGIN_DELETE');?></h3>
			</div>
			<div class="modal-body">
				<?php echo JText::sprintf('COM_JDEVELOPER_PLUGIN_DELETE_DESC');?>
				<br>
				<?php echo JHtml::_('grid.id', 0, $this->item->id); ?> <?php echo JText::_("YES")?>
			</div>
			<div class="modal-footer">
				<button onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_("COM_JDEVELOPER_PLUGIN_DELETE_CONFIRM") ?>');}else{ Joomla.submitbutton('plugins.delete')}" class="btn btn-danger">
					<span class="icon-delete"></span> <?php echo JText::_('JTOOLBAR_DELETE'); ?>
				</button>
				<button class="btn" type="button" data-dismiss="modal">
					<?php echo JText::_('JTOOLBAR_CANCEL'); ?>
				</button>
			</div>
		</div>
		<div>
			<input type="hidden" name="task" value=" " />
			<input type="hidden" name="view" value="<?php echo $input->get("view"); ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>	
	</div>
</form>