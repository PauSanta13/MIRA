<section class="<?php block_classes('home-faro',$block, 'row'); ?>">
	<div class="content">
		
		<h2 class="title">
			<?php echo (!empty($field['title']) ? $field['title'] : 'Última entrada:'); ?>
		</h2>

		<?php $last_post = get_posts(array( 'post_type' => 'post', 'numberposts' => 1 )); ?>

		<?php if(empty($last_post)) {
				echo 'Error: ' . __('Empty');
			} else { ?>
				<div class="arrow-cards">
					<div class="item">
						<?php global $post; the_component('card', null, $last_post); ?>
					</div>
				</div>
		<?php } ?>

	</div>

<?php if($field['button_enabled']) {?>
	<div class="cta text-center clear clearfix">
		<br><br>
		<a href="<?php echo $field['link']; ?>" class="wp-block-button__link btn btn-primary"><?php echo $field['button_text']; ?></a>
	</div>
<?php } ?>

</section>