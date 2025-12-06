<?php // Create align class from block alignment settings
    $align_class = isset($block['align'])? 'align-' . $block['align'] : '';
    $color = isset($field['color'])? 'style="background-color:'.$field['color'].'"': '';
?>
<section class="component clear component-team <?php echo $align_class; ?>" <?php echo $color; ?>>
	<h2 class="title text-center <?php echo (is_color_dark($field['color']) ? 'inverted-text-color' : ''); ?>">
		<?php echo $field['title']; ?>
	</h2>
	<?php shuffle($field['expertos']); ?>
	<?php foreach($field['expertos'] as $e) { ?>
		<?php $info = get_fields($e['post']->ID); ?>
		<article class="experto">
			<figure class="experto-img cell editor-hide lax" data-lax-preset=" blurIn">
				<?php echo get_the_post_thumbnail($e['post']->ID); ?>
			</figure>
			<div class="experto-info cell" >
				<h2>
					<a href="<?php echo $info['linkedpost']; ?>" class="arrow-link">
						<?php echo $e['post']->post_title; ?>
					</a>
				</h2>
				<div class="text" >
					<?php echo apply_filters('the_content',$e['custom_text_enable'] ? $e['custom_text'] : $e['post']->post_content); ?>
				</div>
				<hr />
				<div class="contact">
					<div class="vias">
						<div class="via p">
							<?php if(!empty($info['phone'])) { ?>
								<strong>Llama:</strong>
								<span><a href="tel:<?php echo $info['phone']; ?>"><?php echo $info['phone']; ?></a></span>
							<?php }; ?>
						</div>
						<div class="via m">
							<strong>Email:</strong>
							<?php if(!empty($info['email'])) { ?>
								<span><a href="mailto:<?php echo $info['email']; ?>"><?php echo $info['email']; ?></a></span>
							<?php } else echo 'eMail error'; ?>
						</div>
					</div>
					<div class="action editor-hide">
						<div class="color" style="border-color: <?php echo $field['color']; ?>; background:<?php echo $field['color']; ?>">
							<a class="wp-block-button__link btn btn-white" href="mailto:<?php echo $info['email']; ?>">Consultas</a>
						</div>
					</div>
				</div>
			</div>
		</article> 
	<?php } ?>
</section>