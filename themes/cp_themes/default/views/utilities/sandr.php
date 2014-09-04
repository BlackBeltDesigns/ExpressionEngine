<?php extend_template('default-nav'); ?>

<h1><?=$cp_page_title?> <span class="required intitle">&#10033; <?=lang('required_fields')?></span></h1>
<?=form_open(cp_url('utilities/sandr'), 'class="settings ajax-validate"')?>
	<div class="alert inline warn">
		<?=lang('sandr_warning')?>
	</div>
	<?php $this->view('_shared/alerts')?>
	<fieldset class="col-group <?=form_error_class('search_term')?>">
		<div class="setting-txt col w-16">
			<h3><?=lang('sandr_search_text')?> <span class="required" title="required field">&#10033;</span></h3>
		</div>
		<div class="setting-field col w-16 last">
			<textarea name="search_term" cols="" rows=""><?=set_value('search_term')?></textarea>
			<?=form_error('search_term')?>
		</div>
	</fieldset>
	<fieldset class="col-group <?=form_error_class('replace_term')?>">
		<div class="setting-txt col w-16">
			<h3><?=lang('sandr_replace_text')?> <span class="required" title="required field">&#10033;</span></h3>
		</div>
		<div class="setting-field col w-16 last">
			<textarea name="replace_term" cols="" rows=""><?=set_value('replace_term')?></textarea>
			<?=form_error('replace_term')?>
		</div>
	</fieldset>
	<fieldset class="col-group last <?=form_error_class('replace_where')?>">
		<div class="setting-txt col w-8">
			<h3><?=lang('sandr_in')?> <span class="required" title="required field">&#10033;</span></h3>
			<em><?=lang('sandr_in_desc')?></em>
		</div>
		<div class="setting-field col w-8 last">
			<select name="replace_where">
				<?php foreach ($replace_options as $label => $option): ?>
					<option value="">----</option>
					<?php if ( ! isset($option['choices'])): ?>
						<option value="<?=$label?>"<?=set_select('replace_where', $label)?>><?=$option['name']?></option>
					<?php else: ?>
						<option value=""><?=$option['name']?> <?=lang('choose_below')?></option>
						<?php foreach ($option['choices'] as $value => $text): ?>
						<option value="<?=$value?>" <?=set_select('replace_where', $value)?>>&nbsp;&nbsp;&nbsp;&nbsp;<?=$text?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<?=form_error('replace_where')?>
		</div>
	</fieldset>

	<fieldset class="form-ctrls <?=form_error_class('password_auth')?>">
		<div class="password-req">
			<div class="setting-txt col w-8">
				<h3><?=lang('current_password')?> <span class="required" title="required field">&#10033;</span></h3>
				<em><?=lang('sandr_password_desc')?></em>
			</div>
			<div class="setting-field col w-8 last">
				<input class="required" name="password_auth" type="password" value="">
				<?=form_error('password_auth')?>
			</div>
		</div>
		<?=cp_form_submit('btn_sandr', 'btn_sandr_working')?>
	</fieldset>
</form>