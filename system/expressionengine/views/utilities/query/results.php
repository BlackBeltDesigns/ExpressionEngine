<?php extend_template('default-nav', 'outer_box'); ?>

<div class="box mb">
	<h1><?=lang('sql_query_abbr')?></h1>
	<div class="txt-wrap">
		<ul class="checklist">
			<li><?=$thequery?></li>
			<li class="last">
				<?php if ($write): ?>
					<b><?=lang('affected_rows')?>:</b> <?=$affected?>
				<?php else: ?>
					<b><?=lang('total_results')?>:</b> <?=$total_results?>
				<?php endif ?>
			</li>
		</ul>
	</div>
</div>
<div class="box">
	<div class="tbl-ctrls">
		<?=form_open($table['base_url'])?>
			<?php if ( ! $write): ?>
				<fieldset class="tbl-search right">
					<input placeholder="<?=lang('type_phrase')?>" type="text" name="search" value="<?=$table['search']?>">
					<input class="btn submit" type="submit" value="<?=lang('search_table')?>">
				</fieldset>
			<?php endif ?>
			<h1><?=$cp_page_title?></h1>
			<?php $this->view('_shared/table', $table); ?>
			<?php $this->view('_shared/pagination'); ?>
		</form>
	</div>
</div>