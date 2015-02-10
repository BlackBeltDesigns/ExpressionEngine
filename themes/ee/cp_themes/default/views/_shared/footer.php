		</section>
		<section class="product-bar">
			<div class="snap">
				<div class="left">
					<p><b>ExpressionEngine</b> <span class="version" title="About ExpressionEngine"><?=$formatted_version?></span></p>
					<!-- use class out-of-date on the version link. -->
					<!-- Waiting to implement until new Pings library is merged -->
					<!-- <div class="version-info">
						<h3>Installed</h3>
						<p>ExpressionEngine <b>3</b>.0<br><em>build date: 07.12.2014</em></p>
						<h3>Latest Version (<a href="" rel="external">download</a>)</h3>
						<p>ExpressionEngine <b>3</b>.0.1<br><em>build date: 09.16.2014</em></p>
						<a href="" class="close">&#10006;</a>
						<div class="status out">out of date</div>
					</div> -->
					<div class="version-info">
						<h3>Installed</h3>
						<p>ExpressionEngine <?=$formatted_version?><br><em><?=lang('build').' '.APP_BUILD?></em></p>
						<a href="" class="close">&#10006;</a>
						<div class="status">current</div>
					</div>
				</div>
				<div class="right"><p><a href="https://support.ellislab.com/bugs/submit" rel="external">Report Bug</a> <b class="sep">&middot;</b> <a href="https://support.ellislab.com" rel="external">New Ticket</a> <b class="sep">&middot;</b> <a href="http://ellislab.com/expressionengine/user-guide/" rel="external">Manual</a></p></div>
			</div>
		</section>
		<section class="footer">
			<div class="snap">
				<div class="left">
					<p>&copy;2003&mdash;<?=date('Y')?> <a href="<?=ee()->cp->masked_url('http://ellislab.com/expressionengine')?>" rel="external">EllisLab</a>, Inc.<br><a class="scroll" href="#top">scroll to top</a></p>
				</div>
				<div class="right">
					<p><?=lang('license_no')?>:
						<?php if (ee()->config->item('license_number')): ?>
							<?=ee()->config->item('license_number')?>
						<?php elseif (ee()->cp->allowed_group('can_access_admin', 'can_access_sys_prefs')): ?>
							<a href="<?=cp_url('settings/license')?>"><?=lang('register_now')?></a>
						<?php else: ?>
							<?=lang('not_entered')?>
						<?php endif ?>
						<?php if (ee()->config->item('license_contact')): ?>
							<br><?=lang('owned_by')?>: <a href="mailto:<?=ee()->config->item('license_contact')?>">
								<?=(ee()->config->item('license_contact_name')) ?: ee()->config->item('license_contact')?>
							</a>
						<?php endif ?>
					</p>
				</div>
			</div>
		</section>
		<div class="overlay"></div>

		<?=ee()->view->script_tag('jquery/jquery.js')?>
		<?=ee()->view->script_tag('v3/common.js')?>
		<?php
		if (isset($cp_global_js))
		{
			echo $cp_global_js;
		}

		echo $cp_footer_js;

		if (isset($library_src))
		{
			echo $library_src;
		}

		if (isset($script_foot))
		{
			echo $script_foot;
		}

		foreach ($cp_footer_items as $item)
		{
			echo $item."\n";
		}
		?>
		<!--<div id="idle-modal" class="modal-wrap modal-timeout">
			<div class="modal">
				<div class="col-group snap">
					<div class="col w-16 last">
						<a class="m-close" href="#"></a>
						<div class="box">
							<h1>Log into <?=ee()->config->item('site_name')?> <span class="required intitle">&#10033; Required Fields</span></h1>
							<?=form_open('C=login&M=authenticate', array('class' => 'settings'))?>
							<form class="settings" action="">
								<div class="alert inline warn">
									<p>Your administration access session has timed out. Please use the form below to log back into your control panel.</p>
								</div>
								<fieldset class="col-group">
									<div class="setting-txt col w-8">
										<h3>Username <span class="required" title="required field">&#10033;</span></h3>
										<em></em>
									</div>
									<div class="setting-field col w-8 last">
										<input class="required" type="text" value="<?=form_prep(ee()->session->userdata('username'))?>">
									</div>
								</fieldset>
								<fieldset class="col-group last">
									<div class="setting-txt col w-8">
										<h3>Password <span class="required" title="required field">&#10033;</span></h3>
										<em></em>
									</div>
									<div class="setting-field col w-8 last">
										<input class="required" type="password" value="" id="logout-confirm-password">
									</div>
								</fieldset>
								<fieldset class="form-ctrls">
									<?=form_submit('submit', 'Log In', 'class="btn" data-work-text="authenticating..."')?>
								</fieldset>
							<?=form_close()?>
						</div>
					</div>
				</div>
			</div>
		</div>-->
		<?=ee('Alert')->getStandard()?>
	</body>
</html>
