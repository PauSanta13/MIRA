<?php // Create align class from block alignment settings
    $align_class = isset($block['align'])? 'align-' . $block['align'] : '';
    $color = isset($field['color'])? 'background-color:'. $field['color']: '';
?>
<section class="component component-encabezado <?php echo $align_class; ?>"
	style="clear:both; <?php echo $color; ?>">
	<div class="col-12 text-center">
		<h2>
			<?php echo $field['titulo']; ?>
		</h2>
		<?php if(!empty($field['subtitulo'])) { ?>
			<p class="subtitulo"><?php echo $field['subtitulo']; ?></p>
		<?php }; ?>
	</div>
</section>