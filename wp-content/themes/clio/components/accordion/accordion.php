<?php // Create align class from block alignment settings
    $align_class = isset($block['align'])? 'align-' . $block['align'] : '';
    $color = isset($field['color'])? 'style="background:'. $field['color'].'"': '';
?>
<section class="component component-accordion <?php echo $align_class; ?>"  <?php echo $color; ?>>
	<?php if(!empty($field['title']) || !empty($field['description'])) { ?>
		<div class="block-header text-center <?php echo (is_color_dark($field['color']) ? 'inverted-text-color' : ''); ?>">
			<?php if($field['title']) { ?>
				<h2><?php echo apply_filters('the_title',$field['title']); ?></h2>
			<?php } ?>
			<?php if($field['description']) { ?>
				<div class="description"><?php echo $field['description']; ?></div>
			<?php } ?>
		</div>
	<?php } ?>
	<br/>
	<div class="accordion">
		<?php foreach($field['accordion'] as $i => $row) { ?>
			<div class="item">
				<h3 class="title"><?php echo $row['title']; ?></h3>
				<div class="content"><?php echo $row['text']; ?></div>
			</div>
		<?php } ?>
	</div>
	<br/>
</section>