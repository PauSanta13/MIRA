<?php $color = isset($field['color'])? 'style="background-color:'.$field['color'].'"': ''; ?>
<section class="row <?php block_classes('post-selection',$block); ?>" <?php echo $color; ?>>
		<h2 class="title text-center <?php echo (is_color_dark($field['color']) ? 'inverted-text-color' : ''); ?>">
			<?php echo (!empty($field['title']) ? $field['title'] : 'Te puede interesar...'); ?>
		</h2>
		
		<?php if($field['show'] != 'selection') {
			$args = array(
				'post_type'		=> 'post',
				'numberposts'	=> (!empty($field['amount']) && is_numeric($field['amount']) ? $field['amount'] : 6),
				'category'		=> (!empty($field['cat']) && is_int($field['cat']) ? $field['cat'] : ''),
			);
			$field['selection'] = get_posts($args);
		} ?>

		<?php if(empty($field['selection'])) { ?>
			<div class="slider">
				<div class="item">
					<?php echo 'Error: ' . __('Empty'); ?>
				</div>
			</div>
		<?php } else { ?>
			<div class="slider arrow-cards">
				<?php foreach($field['selection'] as $item) { ?>
					<div class="item">
						<?php global $post; the_component('card', null, array($item)); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

	<?php if($field['button_enabled']) {?>
		<div class="cta text-center">
			<a href="<?php echo $field['link']; ?>" class="wp-block-button__link btn btn-primary"><?php echo $field['button_text']; ?></a>
		</div>
	<?php } ?>

</section>