<?php // Create align class from block alignment settings
	$align_class = isset($block['align'])? ' align-' . $block['align'] : '';
	if(!empty($block['className'])) $align_class .= ' '. $block['className']; 
?>
<section class="row component component-grid <?php echo $field['colour'].$align_class;?>">

	<div class="grid-container" >
		<?php if(!empty($field['title']) || !empty($field['description'])) { ?>
			<div class="grid-top col-xs-12 text-center" >
				<?php if($field['title']) { ?>
					<h2 class="title">
						<?php echo apply_filters('the_title',$field['title']); ?>
					</h2>
				<?php } ?>
				<?php if($field['description']) { ?>
					<div class="description"><?php echo $field['description']; ?></div>
				<?php } ?>
			</div>
		<?php } ?>
		<?php /** Grid mechanism, inspired on Daz's 'image-text-repeater-content' cols **/
		if($field['content'] == 'cards') {
			$amount = count($field['cards']);
		} else $amount = count($field['grid']);
		$col = "col-xs-12 col-sm-6"; // whole row on 'xs' and 2 cols per row on 'sm'
		if($amount%2==1 && $amount%3!=0) $odd = --$amount; // number is odd and not multiple of 3
		if($amount%3==0) $col .= " col-sm-4"; // 3 cols per row  on 'sm'
		if($amount%4==0) $col .= " col-lg-3"; // 4 cols per row  on 'lg'
		if($field['content'] == 'cards') {
			foreach($field['cards'] as $i => $card) {
				if(isset($odd) && $i === $odd) $col = "col-xs-12 text-center"; // When $odd, the last cell occupies a whole row. ?>
				<div class="grid-card arrow-cards <?php echo $col; ?> item">
                	<?php the_component('card', null, array($card)); ?>
				</div>
			<?php }
		} else foreach($field['grid'] as $i => $cell) {
			if(isset($odd) && $i === $odd) $col = "col-xs-12 text-center"; // When $odd, the last cell occupies a whole row. ?>
				<div class="grid-cell <?php echo $col; if($cell['graphic']) echo ' has-img'; ?>">
					<?php if($cell['graphic']) { ?>
						<figure class="cell-figure">
							<img src="<?php echo $cell['graphic']; ?>" alt="(icon)" />
						</figure>
					<?php } ?>
					<div class="content">
						<?php if($cell['title']) { ?>
							<h3 class="cell-title"><?php echo $cell['title']; ?></h3>
						<?php } ?>
						<?php echo $cell['description']; ?>
					</div>
				</div>
		<?php } ?>

		<?php if($field['button_enabled']) {?>
			<div class="text-center">
				<a href="<?php echo $field['link']; ?>" class="wp-block-button__link btn btn-primary"><?php echo $field['button_text']; ?></a>
			</div>
		<?php } ?>
	</div>
</section>