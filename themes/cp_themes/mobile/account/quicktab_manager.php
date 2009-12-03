<?php $this->load->view('account/_header')?>



<?=form_open('C=myaccount'.AMP.'M=quicktab_update', '', $form_hidden)?>

<p class="pad"><?=lang('tab_manager_description')?></p>
<p class="pad"><?=lang('tab_manager_instructions')?></p>
<p class="pad container"><?=lang('tab_manager_description_more')?></p>

<?php if (count($quicktabs) > 0):?>
	<p class="pad"><?=lang('quicklinks_delete_instructions')?></p>

	<?php
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(
			lang('tab_title'), 
			lang('tab_order')
		);

		foreach ($quicktabs as $tab):?>

		<ul>
			<li><?=form_input('title_'.$tab['order'], $tab['title'])?></li>
			<li><?=lang('tab_order')?><br />
				<?=form_input('order_'.$tab['order'], $tab['order'])?></li>
		</ul>
		<?php endforeach;?>

		<?=form_submit('quicktabs_submit', lang('update'), 'class="whiteButton"')?>
<?php endif;?>
<?=form_close()?>

</div>	
<?php
if ($EE_view_disable !== TRUE)
{
	$this->load->view('_shared/accessories');
	$this->load->view('_shared/footer');
}

/* End of file quicktab_manager.php */
/* Location: ./themes/cp_themes/default/account/quicktab_manager.php */