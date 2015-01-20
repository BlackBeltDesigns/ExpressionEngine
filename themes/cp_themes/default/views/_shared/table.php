<?php use EllisLab\ExpressionEngine\Library\CP\Table;
if ($wrap): ?>
	<div class="tbl-wrap<?php if ($grid_input): ?> pb<?php endif?>">
<?php endif ?>

<?php if (empty($columns) && empty($data)): ?>
	<table cellspacing="0" class="empty no-results">
		<tr>
			<td>
				<?=lang($no_results['text'])?>
				<?php if ( ! empty($no_results['action_text'])): ?>
					<a class="btn action" href="<?=$no_results['action_link']?>"><?=lang($no_results['action_text'])?></a>>
				<?php endif ?>
			</td>
		</tr>
	</table>
<?php else: ?>
	<table cellspacing="0"<?php if ($grid_input): ?> id="<?=$grid_field_name?>" class="grid-input-form"<?php endif?>>
		<tr>
			<?php if ($reorder): ?>
				<th class="first reorder-col"></th>
			<?php endif ?>
			<?php foreach ($columns as $label => $settings): ?>
				<?php if ($settings['type'] == Table::COL_CHECKBOX): ?>
					<th class="check-ctrl">
						<?php if ( ! empty($data)): // Hide checkbox if no data ?>
							<input type="checkbox" title="select all">
						<?php endif ?>
					</th>
				<?php else: ?>
					<?php
					$table_class = '';
					if ($settings['type'] == Table::COL_ID)
					{
						$table_class .= ' id-col';
					}
					if ($sortable && $settings['sort'] && $sort_col == $label)
					{
						$table_class .= ' highlight';
					} ?>
					<th<?php if ( ! empty($table_class)): ?> class="<?=trim($table_class)?>"<?php endif ?>>
						<?=($lang_cols) ? lang($label) : $label ?>
						<?php if (isset($settings['desc'])): ?>
							<em class="grid-instruct"><?=lang($settings['desc'])?></em>
						<?php endif ?>
						<?php if ($sortable && $settings['sort'] && $base_url != NULL): ?>
							<?php
							$arrow_dir = ($sort_col == $label) ? $sort_dir : 'desc';
							$link_dir = ($arrow_dir == 'asc') ? 'desc' : 'asc';
							$base_url->setQueryStringVariable('sort_col', $label);
							$base_url->setQueryStringVariable('sort_dir', $link_dir);
							?>
							<a href="<?=$base_url?>" class="ico sort <?=$arrow_dir?> right"></a>
						<?php endif ?>
					</th>
				<?php endif ?>
			<?php endforeach ?>
			<?php if ($grid_input && ! empty($data)): ?>
				<th class="last grid-remove"></th>
			<?php endif ?>
		</tr>
		<?php
		// Output this if Grid input so we can dynamically show it via JS
		if (empty($data) OR $grid_input): ?>
			<tr class="no-results">
				<td class="solo" colspan="<?=count($columns)?>">
					<?=lang($no_results['text'])?>
					<?php if ( ! empty($no_results['action_text'])): ?>
						<a class="btn<?php if ( ! empty($no_results['action_link'])): ?> action<?php endif?>" href="<?=$no_results['action_link']?>"><?=lang($no_results['action_text'])?></a>
					<?php endif ?>
				</td>
			</tr>
		<?php endif ?>
		<?php foreach ($data as $heading => $rows): ?>
			<?php if ( ! $subheadings)
			{
				$rows = array($rows);
			} 
			if ( ! empty($heading)): ?>
				<tr class="sub-heading"><td colspan="<?=count($columns)?>"><?=lang($heading)?></td></tr>
			<?php endif ?>
			<?php foreach ($rows as $row): ?>
				<tr<?php foreach ($row['attrs'] as $key => $value):?> <?=$key?>="<?=$value?>"<?php endforeach; ?>>
					<?php if ($reorder): ?>
						<td class="reorder-col"><span class="ico reorder"></span></td>
					<?php endif ?>
					<?php foreach ($row['columns'] as $column): ?>
						<?php if ($column['encode'] == TRUE): ?>
							<td><?=htmlspecialchars($column['content'])?></td>
						<?php elseif ($column['type'] == Table::COL_TOOLBAR): ?>
							<td>
								<?=ee()->load->view('_shared/toolbar', $column, TRUE)?>
							</td>
						<?php elseif ($column['type'] == Table::COL_CHECKBOX): ?>
							<td>
								<input
									name="<?=$column['name']?>"
									value="<?=$column['value']?>"
									<?php if (isset($column['data'])):?>
										<?php foreach ($column['data'] as $key => $value): ?>
											data-<?=$key?>="<?=$value?>"
										<?php endforeach; ?>
									<?php endif; ?>
									<?php if (isset($column['disabled'])):?>
										disabled="disabled"
									<?php endif; ?>
									type="checkbox"
								>
							</td>
						<?php elseif ($column['type'] == Table::COL_STATUS): ?>
							<?php $class = isset($column['class']) ? $column['class'] : $column['content']; ?>
							<td><span class="st-<?=$class?>"><?=$column['content']?></span></td>
						<?php elseif (isset($column['html'])): ?>
							<td<?php if (isset($column['error']) && ! empty($column['error'])): ?> class="invalid"<?php endif ?>>
								<?=$column['html']?>
								<?php if (isset($column['error']) && ! empty($column['error'])): ?>
									<em class="ee-form-error-message"><?=$column['error']?></em>
								<?php endif ?>
							</td>
						<?php else: ?>
							<td><?=$column['content']?></td>
						<?php endif ?>
					<?php endforeach ?>
					<?php if ($grid_input): ?>
						<td>
							<ul class="toolbar">
								<li class="remove"><a href="#" title="remove row"></a></li>
							</ul>
						</td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
		<?php endforeach ?>
	</table>
<?php endif ?>

<?php if ($wrap): ?>
	</div>
<?php endif ?>

<?php if ($grid_input && ! empty($data)): ?>
	<ul class="toolbar">
		<li class="add"><a href="#" title="add new row"></a></li>
	</ul>
<?php endif ?>