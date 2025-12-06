<footer id="bottom-footer" class="row component component-footer">

	<!-- Content row -->
	<div class="row content-row">
		<div class="col col-logo">
			<a href="<?php bloginfo('url'); ?>">
				<img src="<?php echo get_bloginfo('template_url'). '/assets/img/header/sphere.png'; ?>"
					alt ="" class="logo" style="width: 5em"/>
			</a>
		</div>
		<?php $cols = get_field('columnas','option');
			if(!empty($cols)) foreach($cols as $key=>$col) { ?>
				<div class="col col-<?php echo 1+$key; ?>">
					<h4><?php echo $col['columna']; ?></h4>
					<?php if(!empty($col['contenido'])) foreach($col['contenido'] as $layout) { ?>
						<div class="layout layout-<?php echo $layout['acf_fc_layout']; ?>">
							<?php
								if($layout['acf_fc_layout'] == 'menu') wp_nav_menu();
								elseif($layout['acf_fc_layout'] == 'texto') echo $layout['texto'];
								elseif($layout['acf_fc_layout'] == 'social') the_component('social', null, get_field('footer_social','option'));
							?>
						</div>
					<?php } ?>
				</div>
		<?php } ?>
	</div>
	<!-- Second row -->
	<div class="row scode-row">
		<div class="scode text-justify">
			<?php 
				echo get_field('codigo_sanitario','option');
			?>
		</div>
	</div>
	<!-- Third row -->
	<div class="row legal-row">
		<div class="legal text-center">
			<?php 
				$legal = get_field('legal','option');
				$legal = str_replace(
					array('{copy}',	'{year}','{/}'),
					array('&copy;',	date("Y"), '<b class="salto"></b>'),
					$legal);
				echo $legal;
			?>
		</div>
	</div>
</footer>