<section class="component component-social">
	<?php if(!empty($field['title'])) { ?>
		<strong class="title"><?php echo $field['title']; ?></strong>
	<?php } ?>
	<ul style="list-style:none">
		<?php if(!empty($field['links'])) foreach($field['links'] as $link) { ?>
			<li>
				<a href="<?php echo $link['url']; ?>" target="_blank" rel="nofollow">
					<i class="icom icom-<?php echo preg_replace("/[\W]/",'',$link['icono']); ?>"></i>
					<span><?php echo $link['texto']; ?></span>
				</a>
			</li>
		<?php } ?>
	</ul>
</section>