<?php extend_template('wrapper'); ?>

<div class="home-layout">
	<div class="col-group snap mb">
		<div class="col w-16 last">
			<div class="box full">
				<?php if (isset($form_url)): ?>
					<?=form_open($form_url)?>
						<fieldset class="tbl-search right">
							<input placeholder="<?=lang('type_phrase')?>" type="text" name="search" value="<?=ee()->input->get_post('search')?>">
							<input class="btn submit" type="submit" value="<?=lang('search_content')?>">
						</fieldset>
				<?php endif ?>
					<h1>
						<?=$cp_page_title?>
						<ul class="toolbar">
							<li class="solo settings"><a href="<?=cp_url('settings/general')?>" title="<?=lang('settings')?>"></a></li>
						</ul>
					</h1>
				<?php if (isset($form_url)): ?>
				</form>
				<?php endif ?>
			</div>
		</div>
	</div>
	<div class="col-group snap mb">
		<div class="col w-16 last">
			<div class="box">
				<h1><?=lang('comments')?> <a class="btn action" href="<?=cp_url('publish/comments')?>"><?=lang('review_all_new')?></a></h1>
				<div class="info">
					<p><?=lang('there_were')?> <b><?=$number_of_new_comments?></b> <a href="<?=cp_url('publish/comments')?>"><?=lang('new_comments')?></a> <?=lang('since_last_login')?> (March, 19th 2014)</p>
					<p class="last"><b><?=$number_of_pending_comments?></b> <?=lang('are')?> <a href="<?=cp_url('publish/comments', array('filter_by_status' => 'p'))?>"><?=lang('awaiting_moderation')?></a>, <?=lang('and')?> <b><?=$number_of_spam_comments?></b> <?=lang('have_been')?> <a href="http://localhost/el-projects/ee-cp/views/publish-comments-spam.php"><?=lang('flagged_as_spam')?></a>.</p>
				</div>
			</div>
		</div>
	</div>
	<div class="col-group snap mb">
		<div class="col w-8">
			<div class="box">
				<h1><?=lang('channels')?> <a class="btn action" href="<?=cp_url('channel/create')?>"><?=lang('create_new')?></a></h1>
				<div class="info">
					<p><?=lang('channels_desc')?></p>
					<h2><?=ee()->config->item('site_name')?> <?=lang('has')?>:</h2>
					<ul class="arrow-list">
						<li><a href="<?=cp_url('channel')?>"><b><?=$number_of_channels?></b> <?=lang('channels')?></a></li>
						<li><a href="<?=cp_url('channel/field')?>"><b><?=$number_of_channel_fields?></b> <?=lang('channel_fields')?></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col w-8 last">
			<div class="box">
				<h1><?=lang('members')?> <a class="btn action" href="http://localhost/el-projects/ee-cp/views/members-new.php"><?=lang('register_new')?></a></h1>
				<div class="info">
					<p><?=sprintf(lang('members_desc'), cp_url('settings/members'))?></p>
					<h2><?=ee()->config->item('site_name')?> <?=lang('has')?>:</h2>
					<ul class="arrow-list">
						<li><a href="<?=cp_url('members')?>"><b><?=$number_of_members?></b> <?=lang('members')?></a></li>
						<li><a href="<?=cp_url('members', array('group' => 2))?>"><b><?=$number_of_banned_members?></b> <?=lang('banned_members')?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="col-group snap">
		<div class="col w-16 last">
			<div class="box">
				<h1><?=lang('content')?> <a class="btn action" href="http://localhost/el-projects/ee-cp/views/publish.php"><?=lang('create_new')?></a></h1>
				<div class="info">
					<p><?=lang('content_desc')?></p>
					<h2><?=ee()->config->item('site_name')?> <?=lang('has')?>:</h2>
					<ul class="arrow-list">
						<li><a href="<?=cp_url('publish/edit')?>"><b><?=$number_of_entries?></b> <?=sprintf(lang('entries_with_comments'), $number_of_comments)?></a></li>
						<li><a href="<?=cp_url('publish/edit', array('filter_by_status' => 'closed'))?>"><b><?=$number_of_closed_entries?></b> <?=sprintf(lang('closed_entries_with_comments'), $number_of_comments_on_closed_entries)?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

/* End of file homepage.php */
/* Location: ./themes/cp_themes/default/homepage.php */