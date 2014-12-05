<div class="box mb">
	<?php $this->ee_view('_shared/form')?>
</div>
<div class="box snap">
	<div class="tbl-ctrls">
		<?=form_open(cp_url('addons/settings/rte/update_toolsets'))?>
			<fieldset class="tbl-search right">
				<a class="btn tn action" href="<?=cp_url('addons/settings/rte/new_toolset')?>"><?=lang('create_new')?></a>
			</fieldset>
			<h1><?=lang('available_tool_sets')?></h1>
			<?php $this->ee_view('_shared/table', $table); ?>
			<?php $this->ee_view('_shared/pagination'); ?>
			<fieldset class="tbl-bulk-act">
				<select name="bulk_action">
					<option value="">-- <?=lang('with_selected')?> --</option>
					<option value="enable"><?=lang('enable')?></option>
					<option value="disable"><?=lang('disable')?></option>
					<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
				</select>
				<input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
			</fieldset>
		<?=form_close();?>
	</div>
</div>

<div class="modal-confirm-remove" style="display:none">
	<?php // Modal for removal confirmation
	$modal_vars = array(
		'form_url'	=> cp_url('addons/settings/rte/update_toolsets'),
		'hidden'	=> array(
			'bulk_action'	=> 'remove'
		),
		'checklist'	=> array(
			array(
				'kind' => '',
				'desc' => ''
			)
		)
	);

	// $modals['modal-confirm-all'] = $this->ee_view('_shared/modal_confirm_remove', $modal_vars, TRUE);
	// $this->vars(array('modals' => $modals));
	$this->ee_view('_shared/modal_confirm_remove', $modal_vars);
	?>
</div>