<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/header');
}
?>
<div id="edit" class="current">
	<div class="toolbar">
		<h1><?=$cp_page_title?></h1>
		<a href="<?=BASE.AMP?>C=admin_content&amp;M=channel_management" class="back"><?=lang('back')?></a>
        <a class="button" id="infoButton" href="<?=BASE.AMP.'C=login'.AMP.'M=logout'?>"><?=lang('logout')?></a>
	</div>
		<?php $this->load->view('_shared/right_nav')?>
		<?php $this->load->view('_shared/message');?>

		<?=form_open('C=admin_content'.AMP.'M=channel_edit', array('id'=>'channel_prefs'), $form_hidden)?>
		<h3 style="margin-bottom:15px" class="accordion"><?=lang('channel_base_setup')?></h3>
		<div>
			<div class="label">
				<?=required().lang('channel_title', 'channel_title').form_error('channel_title')?>
			</div>

			<ul class="rounded">
				<li><?=form_input(array('id'=>'channel_title','name'=>'channel_title','class'=>'fullfield', 'value'=>set_value('channel_title', $channel_title)))?>
			</ul>

			<div class="label">
				<?=required().lang('channel_name', 'channel_name').form_error('channel_name')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'channel_name','name'=>'channel_name','class'=>'fullfield', 'value'=>set_value('channel_name', $channel_name)))?>
			</ul>

			<div class="label">
				<?=lang('channel_description', 'channel_description')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'channel_description','name'=>'channel_description','class'=>'fullfield', 'value'=>$channel_description))?>
			</ul>

			<div class="label">
				<?=lang('channel_lang', 'channel_lang')?>
			</div>
			<ul class="rounded">
				<li><?=$this->functions->encoding_menu('channel_lang', $channel_lang)?>
			</ul>
		</div>

		<h3 class="accordion"><?=lang('paths')?></h3>
		<div>
			<div class="label">
				<?=lang('channel_url', 'channel_url').'<br />'.lang('channel_url_exp');?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'channel_url','name'=>'channel_url','class'=>'fullfield', 'value'=>$channel_url))?></li>
			</ul>

			<div class="label">
				<?=lang('comment_url', 'comment_url').'<br />'.lang('comment_url_exp')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'comment_url','name'=>'comment_url','class'=>'fullfield', 'value'=>$comment_url))?></li>
			</ul>

			<div class="label">
				<?=lang('search_results_url', 'search_results_url').'<br />'.lang('search_results_url_exp')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'search_results_url','name'=>'search_results_url','class'=>'fullfield', 'value'=>$search_results_url))?></li>
			</ul>
		
			<div class="label">
				<?=lang('ping_return_url', 'ping_return_url').'<br />'.lang('ping_return_url_exp')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'ping_return_url','name'=>'ping_return_url','class'=>'fullfield', 'value'=>$ping_return_url))?></li>
			</ul>
		
			<div class="label">
				<?=lang('rss_url', 'rss_url').'<br />'.lang('rss_url_exp')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'rss_url','name'=>'rss_url','class'=>'fullfield', 'value'=>$rss_url))?></li>
			</ul>

			<div class="label">
				<?=lang('live_look_template', 'live_look_template')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('live_look_template', $live_look_template_options, $live_look_template, 'id="live_look_template"')?></li>
			</ul>
		</div>

		<h3 class="accordion"><?=lang('default_settings')?></h3>
		<div>
			<div class="label">
				<?=lang('deft_status', 'deft_status')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('deft_status', $deft_status_options, $deft_status, 'id="deft_status"')?></li>
			</ul>

			<div class="label">
				<?=lang('deft_category', 'deft_category')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('deft_category', $deft_category_options, $deft_category, 'id="deft_category"')?></li>
			</ul>

			<div class="label">
				<?=lang('deft_comments', 'deft_comments')?>
			</div>
			<ul class="rounded">
				<li><?=lang('yes', 'deft_comments_y').NBS.form_radio(array('name'=>'deft_comments', 'id'=>'deft_comments_y', 'value'=>'y', 'checked'=>($deft_comments == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;?>
					<?=lang('no', 'deft_comments_n').NBS.form_radio(array('name'=>'deft_comments', 'id'=>'deft_comments_n', 'value'=>'n', 'checked'=>($deft_comments == 'n') ? TRUE : FALSE))?></li>
			</ul>

			<div class="label">
				<?=lang('search_excerpt', 'search_excerpt')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('search_excerpt', $search_excerpt_options, $search_excerpt, 'id="search_excerpt"')?></li>
			</ul>
		</div>
		
		<h3 class="accordion"><?=lang('channel_settings')?></h3>
		<div>
			<div class="label">
				<?=lang('channel_html_formatting', 'channel_html_formatting')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('channel_html_formatting', $channel_html_formatting_options, $channel_html_formatting, 'id="channel_html_formatting"')?></li>
			</ul>

			<div class="label">
				<?=lang('channel_allow_img_urls', 'channel_allow_img_urls')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'channel_allow_img_urls_y').NBS.form_radio(array('name'=>'channel_allow_img_urls', 'id'=>'channel_allow_img_urls_y', 'value'=>'y', 'checked'=>($channel_allow_img_urls == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'channel_allow_img_urls_n').NBS.form_radio(array('name'=>'channel_allow_img_urls', 'id'=>'channel_allow_img_urls_n', 'value'=>'n', 'checked'=>($channel_allow_img_urls == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('auto_link_urls', 'channel_auto_link_urls')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'channel_auto_link_urls_y').NBS.form_radio(array('name'=>'channel_auto_link_urls', 'id'=>'channel_auto_link_urls_y', 'value'=>'y', 'checked'=>($channel_auto_link_urls == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'channel_auto_link_urls_n').NBS.form_radio(array('name'=>'channel_auto_link_urls', 'id'=>'channel_auto_link_urls_n', 'value'=>'n', 'checked'=>($channel_auto_link_urls == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>
		</div>

		<h3 class="accordion"><?=lang('versioning')?></h3>
		<div>
			<div class="label">
				<?=lang('enable_versioning', 'enable_versioning')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'enable_versioning_y').NBS.form_radio(array('name'=>'enable_versioning', 'id'=>'enable_versioning_y', 'value'=>'y', 'checked'=>($enable_versioning == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'enable_versioning_n').NBS.form_radio(array('name'=>'enable_versioning', 'id'=>'enable_versioning_n', 'value'=>'n', 'checked'=>($enable_versioning == 'n') ? TRUE : FALSE));
			
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('max_revisions', 'max_revisions')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = form_input(array('id'=>'max_revisions','name'=>'max_revisions','class'=>'fullfield', 'value'=>$max_revisions));
				$controls .= '<br/>'.form_checkbox(array('name'=>'clear_versioning_data', 'id'=>'clear_versioning_data', 'value'=>'y', 'checked'=>FALSE)).NBS.'<span class="notice">'.lang('clear_versioning_data', 'clear_versioning_data').'</span>';
				?>
				<li><?=$controls?></li>
			</ul>
		</div>
		
		<h3 class="accordion"><?=lang('notification_settings')?></h3>
		<div>
			<div class="label">
				<?=lang('channel_notify', 'channel_notify')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'channel_notify_y').NBS.form_radio(array('name'=>'channel_notify', 'id'=>'channel_notify_y', 'value'=>'y', 'checked'=>($channel_notify == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'channel_notify_n').NBS.form_radio(array('name'=>'channel_notify', 'id'=>'channel_notify_n', 'value'=>'n', 'checked'=>($channel_notify == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>

			<?php if (isset($this->cp->installed_modules['comment'])):?>
			<div class="label">
				<?=lang('comment_notify_emails', 'channel_notify_emails')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'channel_notify_emails','name'=>'channel_notify_emails','class'=>'fullfield', 'value'=>$channel_notify_emails))?></li>
			</ul>

			<div class="label">
				<?=lang('comment_notify', 'comment_notify')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_notify_y').NBS.form_radio(array('name'=>'comment_notify', 'id'=>'comment_notify_y', 'value'=>'y', 'checked'=>($comment_notify == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_notify_n').NBS.form_radio(array('name'=>'comment_notify', 'id'=>'comment_notify_n', 'value'=>'n', 'checked'=>($comment_notify == 'n') ? TRUE : FALSE));			
				?>
				<li><?=$controls?></li>
			</ul>
			
			<div class="label">
				<?=lang('comment_notify_emails', 'comment_notify_emails')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'comment_notify_emails','name'=>'comment_notify_emails','class'=>'fullfield', 'value'=>$comment_notify_emails))?></li>
			</ul>
			<?php endif; ?>
		</div>

		<?php if (isset($this->cp->installed_modules['comment'])):?>
		<h3 class="accordion"><?=lang('comment_prefs')?></h3>
		<div>
			<div class="label">
				<?=lang('comment_system_enabled', 'comment_system_enabled')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_system_enabled_y').NBS.form_radio(array('name'=>'comment_system_enabled', 'id'=>'comment_system_enabled_y', 'value'=>'y', 'checked'=>($comment_system_enabled == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_system_enabled_n').NBS.form_radio(array('name'=>'comment_system_enabled', 'id'=>'comment_system_enabled_n', 'value'=>'n', 'checked'=>($comment_system_enabled == 'n') ? TRUE : FALSE));			
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('comment_require_membership', 'comment_require_membership')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_require_membership_y').NBS.form_radio(array('name'=>'comment_require_membership', 'id'=>'comment_require_membership_y', 'value'=>'y', 'checked'=>($comment_require_membership == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_require_membership_n').NBS.form_radio(array('name'=>'comment_require_membership', 'id'=>'comment_require_membership_n', 'value'=>'n', 'checked'=>($comment_require_membership == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('comment_use_captcha', 'comment_use_captcha').'<br />'.lang('captcha_explanation')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_use_captcha_y').NBS.form_radio(array('name'=>'comment_use_captcha', 'id'=>'comment_use_captcha_y', 'value'=>'y', 'checked'=>($comment_use_captcha == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_use_captcha_n').NBS.form_radio(array('name'=>'comment_use_captcha', 'id'=>'comment_use_captcha_n', 'value'=>'n', 'checked'=>($comment_use_captcha == 'n') ? TRUE : FALSE));			
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('comment_require_email', 'comment_require_email')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_require_email_y').NBS.form_radio(array('name'=>'comment_require_email', 'id'=>'comment_require_email_y', 'value'=>'y', 'checked'=>($comment_require_email == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_require_email_n').NBS.form_radio(array('name'=>'comment_require_email', 'id'=>'comment_require_email_n', 'value'=>'n', 'checked'=>($comment_require_email == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('comment_moderate', 'comment_moderate').'<br />'.lang('comment_moderate_exp')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_moderate_y').NBS.form_radio(array('name'=>'comment_moderate', 'id'=>'comment_moderate_y', 'value'=>'y', 'checked'=>($comment_moderate == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_moderate_n').NBS.form_radio(array('name'=>'comment_moderate', 'id'=>'comment_moderate_n', 'value'=>'n', 'checked'=>($comment_moderate == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('comment_max_chars', 'comment_max_chars')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'comment_max_chars','name'=>'comment_max_chars','class'=>'fullfield', 'value'=>$comment_max_chars))?></li>
			</ul>

			<div class="label">
				<?=lang('comment_timelock', 'comment_timelock').'<br />'.lang('comment_timelock_desc')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'comment_timelock','name'=>'comment_timelock','class'=>'fullfield', 'value'=>$comment_timelock))?></li>
			</ul>

			<div class="label">
				<?=lang('comment_expiration', 'comment_expiration').'<br />'.lang('comment_expiration_desc')?>
			</div>
			<ul class="rounded">
				<?php
			$controls = form_input(array('id'=>'comment_expiration','name'=>'comment_expiration','class'=>'fullfield', 'value'=>$comment_expiration));
			$controls .= '<br/>'.form_checkbox(array('name'=>'apply_expiration_to_existing', 'id'=>'apply_expiration_to_existing', 'value'=>'y', 'checked'=>FALSE)).NBS.'<span class="notice">'.lang('update_existing_comments', 'apply_expiration_to_existing').'</span>';
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('comment_text_formatting', 'comment_text_formatting')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('comment_text_formatting', $comment_text_formatting_options, $comment_text_formatting, 'id="comment_text_formatting"')?></li>
			</ul>

			<div class="label">
				<?=lang('comment_html_formatting', 'comment_html_formatting')?>
			</div>
			<ul class="rounded">
				<li><?=form_dropdown('comment_html_formatting', $comment_html_formatting_options, $comment_html_formatting, 'id="comment_html_formatting"')?></li>
			</ul>

			<div class="label">
				<?=lang('comment_allow_img_urls', 'comment_allow_img_urls')?>
			</div>
			<ul class="rounded">
				<?php
			$controls = lang('yes', 'comment_allow_img_urls_y').NBS.form_radio(array('name'=>'comment_allow_img_urls', 'id'=>'comment_allow_img_urls_y', 'value'=>'y', 'checked'=>($comment_allow_img_urls == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
			$controls .= lang('no', 'comment_allow_img_urls_n').NBS.form_radio(array('name'=>'comment_allow_img_urls', 'id'=>'comment_allow_img_urls_n', 'value'=>'n', 'checked'=>($comment_allow_img_urls == 'n') ? TRUE : FALSE));
				?>
				<li><?=$controls?></li>
			</ul>

			<div class="label">
				<?=lang('auto_link_urls', 'comment_auto_link_urls')?>
			</div>
			<ul class="rounded">
				<?php
				$controls = lang('yes', 'comment_auto_link_urls_y').NBS.form_radio(array('name'=>'comment_auto_link_urls', 'id'=>'comment_auto_link_urls_y', 'value'=>'y', 'checked'=>($comment_auto_link_urls == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
				$controls .= lang('no', 'comment_auto_link_urls_n').NBS.form_radio(array('name'=>'comment_auto_link_urls', 'id'=>'comment_auto_link_urls_n', 'value'=>'n', 'checked'=>($comment_auto_link_urls == 'n') ? TRUE : FALSE));			
				?>
				<li><?=$controls?></li>
			</ul>
		</div>
		<?php endif; ?>

		<h3 class="accordion"><?=lang('publish_page_customization')?></h3>
		<div>
			<?php foreach ($publish_page_customization_options as $option): ?>
				<div class="label">
					<?=lang($option, $option)?>
				</div>
				<ul class="rounded">
					<?php
					$controls = lang('yes', $option.'_y').NBS.form_radio(array('name'=>$option, 'id'=>$option.'_y', 'value'=>'y', 'checked'=>($$option == 'y') ? TRUE : FALSE)).NBS.NBS.NBS.NBS.NBS;
					$controls .= lang('no', $option.'_n').NBS.form_radio(array('name'=>$option, 'id'=>$option.'_n', 'value'=>'n', 'checked'=>($$option == 'n') ? TRUE : FALSE));				
					?>
					<li><?=$controls?></li>
				</ul>
			<?php endforeach;?>
		
			<div class="label">
				<?=lang('default_entry_title', 'default_entry_title')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'default_entry_title','name'=>'default_entry_title','class'=>'fullfield', 'value'=>$default_entry_title))?></li>
			</ul>

			<div class="label">
				<?=lang('url_title_prefix', 'url_title_prefix').'<br />'.lang('single_word_no_spaces').form_error('url_title_prefix')?>
			</div>
			<ul class="rounded">
				<li><?=form_input(array('id'=>'url_title_prefix','name'=>'url_title_prefix','class'=>'fullfield', 'value'=>set_value('url_title_prefix', $url_title_prefix)))?></li>
			</ul>
		</div>
		<?=form_submit(array('name' => 'channel_prefs_submit', 'value' => lang('update'), 'class' => 'whiteButton'))?> 
		<?=form_submit(array('name' => 'return', 'value' => lang('update_and_return'), 'class' => 'whiteButton'))?>
		<?=form_close()?>
</div>	
<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/accessories');
	$this->load->view('_shared/footer');
}

/* End of file channel_management.php */
/* Location: ./themes/cp_themes/default/admin/channel_management.php */