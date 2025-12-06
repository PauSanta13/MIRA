<nav id="topnav" class="component nav">
	<div class="wrapper">
		<a class="logo" href="/">
			<?php
				$tag = is_front_page() ? 'h1' : 'strong';
				echo "<$tag class=\"sr-only\">".get_bloginfo('name')."</$tag>";
			?>
			<b class="esfera"></b>
			<img src="<?php echo get_bloginfo('template_url'). '/assets/img/header/ACIMUT Psicologia Aplicada.png'; ?>" alt ="" />
		</a>
		<button id="menu-icon" class="hamburger togglemenu">
			<span class="bar1"></span><span class="bar2"></span><span class="bar3"></span>
		</button>
		<div id="toplinks">
			<?php wp_nav_menu( array(
				'theme_location' => 'topleft',
				'menu_class' => 'menu topleft',
				'container' => false,
				'link_before' => '<span>',
				'link_after' => '</span>'
				) ) ?>
				<?php wp_nav_menu( array(
					'theme_location' => 'topright',
					'menu_class' => 'menu topright',
					'container' => false,
					'link_before' => '<span>',
					'link_after' => '</span>'
					) ) ?>
			<?php the_component('social', null, get_field('footer_social','option')); ?>
		</div>
	</div>
</nav>