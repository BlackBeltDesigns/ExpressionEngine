<?php extend_template('default-nav'); ?>

<?=form_open(cp_url('utilities/member-import/process-xml'), 'class="tbl-ctrls"', $form_hidden)?>
	<h1><?=$cp_page_title?></h1>
	<?php $this->view('_shared/form_messages')?>
	<?php if ($added_fields && count($added_fields) > 0):?>
		<div class="alert inline success">
			<h3><?=lang('new_fields_success')?></h3>
			<p><?=implode('<br />', $added_fields)?></p>
		</div>
	<?php endif;?>
	<div class="alert inline warn">
		<?=lang(lang('confirm_import_warning'))?>
	</div>
	<table cellspacing="0">
		<tr>
			<th class="first"><?=lang('option')?></th>
			<th class="last"><?=lang('value')?></th>
		</tr>
		<tr>
			<td><?=lang('mbr_xml_file')?></td>
			<td><?=$xml_file?></td>
		</tr>
		<tr class="alt">
			<td><?=lang('member_group')?></td>
			<td><?=$default_group_id?></td>
		</tr>
		<tr>
			<td><?=lang('mbr_language')?></td>
			<td><?=$language?></td>
		</tr>
		<tr class="alt">
			<td><?=lang('mbr_timezone')?></td>
			<td><?=$timezones?></td>
		</tr>
		<tr>
			<td><?=lang('mbr_datetime_fmt')?></td>
			<td><?=$date_format?>, <?=$time_format?></td>
		</tr>
		<tr class="alt last">
			<td class="first"><?=lang('mbr_create_custom_fields')?></td>
			<td class="last"><?=$auto_custom_field?></td>
		</tr>
	</table>

	<fieldset class="form-ctrls">
		<?=cp_form_submit('confirm_import', 'btn_confirm_import_working')?>
	</fieldset>
</form>