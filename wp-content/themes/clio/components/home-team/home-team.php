<?php // Create align class from block alignment settings
	$align_class =  (isset($block['align'])? 'align-' . $block['align'] : '');
	if(!empty($block['class'])) $align_class .= $block['class'];
?>
<section class="row component component-home-team <?php
        echo $align_class;
	?>">
	<div class="top">
		<h2 class="title"><?php if(!empty($field['title'])) {
			echo $field['title'];
		} else {
			echo 'Nuestro equipo de psicólogos';
		} ?></h2>
	</div>
	<div class="wrapper">
		<?php if(!empty($field['fotos'])) { ?> 
			<div class="fotos"> 
				<?php shuffle($field['fotos']); ?>
				<?php foreach($field['fotos'] as $member) { ?>
					<?php if(!empty($member['description'])) { ?>
						<a href="<?php echo $member['description']; ?>">
					<?php } ?>
					<img
						src="<?php echo $member['url']; ?>"
						alt="<?php echo $member['title']; ?>" />
					<?php if(!empty($member['description'])) { ?>
						</a>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if(!empty($field['text'])) { ?>
			<div class="text"><?php echo $field['text']; ?></div>
		<?php }; ?>
		
		<?php if($field['button_enabled']) {?>
			<div class="text-center">
				<a href="<?php echo $field['link']; ?>" class="wp-block-button__link btn btn-primary"><?php echo $field['button_text']; ?></a>
			</div>
		<?php } ?>
	</div>
	<div class="bottom" style="min-height:2em; background-color:<?php echo $field['color_siguiente']; ?>">
	</div>
</section>