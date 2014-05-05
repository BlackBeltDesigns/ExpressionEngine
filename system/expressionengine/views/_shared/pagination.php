<?php if ( ! empty($pagination)): ?>
<div class="paginate">
	<ul>
		<li><a href="<?=$pagination['first']?>"><?=lang('first')?></a></li>
		<?php if ( isset($pagination['prev'])): ?>
		<li><a href="<?=$pagination['prev']?>"><?=lang('prev')?></a></li>
		<?php endif;?>

		<?php foreach ($pagination['pages'] as $page => $link): ?>
		<li><a<?php if($pagination['current_page'] == $page): ?> class="act"<?php endif; ?> href="<?=$link?>"><?=$page?></a></li>
		<?php endforeach; ?>

		<?php if ( isset($pagination['next'])): ?>
		<li><a href="<?=$pagination['next']?>"><?=lang('next')?></a></li>
		<?php endif;?>
		<li><a class="last" href="<?=$pagination['last']?>"><?=lang('last')?></a></li>
	</ul>
</div>
<?php endif; ?>

<?php
/* End of file pagination.php */
/* Location: ./themes/cp_themes/default/_shared/pagination.php */