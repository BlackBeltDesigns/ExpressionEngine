<?php extend_template('login'); ?>

<div class="box snap">
	<h1><?=lang('new_password')?> <span class="ico locked"></span></h1>
	<?php if ( ! empty($messages)):?>
		<div class="alert inline <?=$message_status?>">
			<?php foreach ($messages as $message): ?>
				<p><b>!!</b> <?=$message?></p>
			<?php endforeach ?>
		</div>
	<?php endif;?>
	<?=form_open('C=login'.AMP.'M=reset_password')?>
		<fieldset>
			<?=lang('new_password', 'password')?>
			<?=form_password(array('dir' => 'ltr', 'name' => "password", 'id' => "password", 'maxlength' => 80, 'autocomplete' => 'off'))?>
		</fieldset>
		<fieldset class="last">
			<?=lang('new_password_confirm', 'password_confirm')?>
			<?=form_password(array('dir' => 'ltr', 'name' => "password_confirm", 'id' => "password_confirm", 'maxlength' => 80, 'autocomplete' => 'off'))?>
		</fieldset>
		<fieldset class="form-ctrls">
			<?=form_hidden('resetcode', $resetcode)?>
			<?=form_submit('submit', 'Change Password', 'class="btn" data-work-text="updating..."')?>
		</fieldset>
	<?=form_close()?>
</div>