<?php extend_template('wrapper'); ?>

<?php if (isset($header)): ?>
	<div class="col-group">
		<div class="col w-16 last">
			<div class="box full mb">
				<div class="tbl-ctrls">
					<?php if (isset($header['form_url'])): ?>
						<?=form_open($header['form_url'])?>
							<fieldset class="tbl-search right">
								<input placeholder="<?=lang('type_phrase')?>" type="text" value="">
								<?php if (isset($header['search_button_value'])): ?>
								<input class="btn submit" type="submit" value="<?=$header['search_button_value']?>">
								<?php else: ?>
								<input class="btn submit" type="submit" value="<?=lang('search')?>">
								<?php endif; ?>
							</fieldset>
					<?php endif ?>
						<h1>
							<?=$header['title']?>
							<?php if (isset($header['toolbar_items']))
							{
								echo ee()->load->view('_shared/toolbar', $header, TRUE);
							} ?>
						</h1>
					<?php if (isset($header['form_url'])): ?>
						</form>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
<?php endif ?>

<div class="col-group">
	<div class="col w-16 last">
		<ul class="breadcrumb">
			<?php foreach ($cp_breadcrumbs as $link => $title): ?>
				<li><a href="<?=$link?>"><?=$title?></a></li>
			<?php endforeach ?>
			<li class="last"><?=$cp_page_title?></li>
		</ul>

		<div class="box has-tabs">
			<h1><?=$cp_page_title?> <a class="btn action ta" href=""><?=lang('view_rendered')?></a></h1>
			<div class="tab-bar">
				<ul>
					<li><a class="act" href="" rel="t-0"><?=lang('edit')?></a></li>
					<li><a href="" rel="t-1"><?=lang('notes')?></a></li>
					<li><a href="" rel="t-2"><?=lang('settings')?></a></li>
					<li><a href="" rel="t-3"><?=lang('access')?></a></li>
				</ul>
			</div>
			<?=form_open($form_url, 'class="settings"')?>
				<div class="tab t-0 tab-open">
					<fieldset class="col-group last">
						<div class="setting-txt col w-16">
							<em><?=sprintf(lang('last_edit'), ee()->localize->human_time($template->edit_date), $author->screen_name)?></em>
						</div>
						<div class="setting-field col w-16 last">
							<textarea class="template-edit" cols="" rows="" name="template_data"><?=set_value('template_data', $template->template_data)?></textarea>
						</div>
					</fieldset>
				</div>
				<div class="tab t-1">
					<fieldset class="col-group last">
						<div class="setting-txt col w-16">
							<h3><?=lang('template_notes')?></h3>
							<em><?=lang('template_notes_desc')?></em>
						</div>
						<div class="setting-field col w-16 last">
							<textarea cols="" rows="" name="template_notes"><?=set_value('template_notes', $template->template_notes)?></textarea>
						</div>
					</fieldset>
				</div>
				<div class="tab t-2">
					<?=$settings?>
				</div>
				<div class="tab t-3">
					<?=$access?>
				</div>
				<fieldset class="form-ctrls">
					<input class="btn" type="submit" value="Update Template">
					<input class="btn" type="submit" value="Update &amp; Finish Editing">
					<input class="btn disable" type="submit" value="Fix Errors, Please">
					<input class="btn work" type="submit" value="Saving...">
				</fieldset>
			</form>
		</div>

	</div>
</div>

<?php if (isset($blocks['modals'])) echo $blocks['modals']; ?>