<section class="row component component-fotos">
	<div id="sector-container" class="isotope">
		<?php if($field['fotos']) foreach ($field['fotos'] as $foto) { ?>
			<div class="sector-thumbnail isotope-item natural-frame">
				<div class="img">
					<img 
						src="<?php echo $foto['sizes']['medium'] ?>"
						title="<?php echo $foto['title'] ?>"
						alt="<?php echo $foto['title'] ?>"
						data-full="<?php echo $foto['url'] ?>"
						/>
				</div>
			</div>
		<?php } ?>
	</div>
</section>