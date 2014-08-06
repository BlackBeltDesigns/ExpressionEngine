<?php extend_template('default-nav'); ?>

<?=form_open(cp_url('utilities/stats/sync'), 'class="tbl-ctrls"')?>
	<h1><?php echo isset($cp_heading) ? $cp_heading : $cp_page_title?></h1>
	<?php $this->view('_shared/alerts')?>
	<table cellspacing="0">
		<tr>
			<th<?php if($highlight == 'source'): ?> class="highlight"<?php endif; ?>><?=lang('source')?> <a href="<?=$source_sort_url?>" class="ico sort <?=$source_direction?> right"></a></th>
			<th<?php if($highlight == 'record_count'): ?> class="highlight"<?php endif; ?>><?=lang('record_count')?> <a href="<?=$record_count_sort_url?>" class="ico sort <?=$record_count_direction?> right"></a></th>
			<th><?=lang('manage')?></th>
			<th class="check-ctrl"><input type="checkbox" title="<?=strtolower(lang('select_all'))?>"></th>
		</tr>

		<?php foreach($sources as $source => $count): ?>
		<tr>
			<td><?=lang($source)?></td>
			<td><?=$count?></td>
			<td>
				<ul class="toolbar">
					<li class="sync"><a href="<?=cp_url('utilities/stats/sync/' . $source)?>" title="<?=strtolower(lang('sync'))?>"></a></li>
				</ul>
			</td>
			<td><input type="checkbox" name="selection[]" value="<?=$source?>"></td>
		</tr>
		<?php endforeach; ?>

	</table>

	<?php $this->view('_shared/pagination'); ?>
	<fieldset class="tbl-bulk-act">
		<select name="bulk_action">
			<option value="">-- <?=lang('with_selected')?> --</option>
			<option value="sync"><?=lang('sync')?></option>
		</select>
		<input class="btn submit" type="submit" value="<?=lang('submit')?>">
	</fieldset>
<?=form_close()?>