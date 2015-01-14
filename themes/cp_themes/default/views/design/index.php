<?php extend_template('default-nav'); ?>

<div class="tbl-ctrls">
	<?=form_open($form_url)?>
		<?php if($show_new_template_button): ?>
		<fieldset class="tbl-search right">
			<a class="btn tn action" href="<?=cp_url('design/template/create/' . $group_id)?>"><?=lang('create_new_template')?></a>
		</fieldset>
		<?php endif; ?>
		<h1><?=$cp_heading?></h1>
		<?=ee('Alert')->getAllInlines()?>
		<?php $this->view('_shared/table', $table); ?>
		<?php $this->view('_shared/pagination'); ?>
		<?php if ( ! empty($table['columns']) && ! empty($table['data'])): ?>
		<fieldset class="tbl-bulk-act">
			<select name="bulk_action">
				<option value="">-- <?=lang('with_selected')?> --</option>
				<option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove-template"><?=lang('remove')?></option>
				<option value="export"><?=lang('export_templates')?></option>
				<option value="sync"><?=lang('sync_templates')?></option>
			</select>
			<button class="btn submit" data-conditional-modal="confirm-trigger"><?=lang('submit')?></button>
		</fieldset>
		<?php endif; ?>
	<?=form_close()?>
</div>

<?php $this->startOrAppendBlock('modals'); ?>

<div class="modal-wrap modal-template-settings">
	<div class="modal">
		<div class="col-group">
			<div class="col w-16">
				<a class="m-close" href="#"></a>
				<div class="box">
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$modal_vars = array(
	'name'		=> 'modal-confirm-remove-template',
	'form_url'	=> $form_url,
	'hidden'	=> array(
		'bulk_action'	=> 'remove'
	)
);

$this->ee_view('_shared/modal_confirm_remove', $modal_vars);
?>

<?php $this->endBlock(); ?>