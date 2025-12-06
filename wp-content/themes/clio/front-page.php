<?php 
// This template is suppose to serve  only the home page
get_header(); ?>

<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	<?php
		global $fields;
		if(function_exists("get_fields")) {
			$fields = get_fields();
		} else {
			die('ACF plugin needed');
		}
	?>
	<article id="page-<?php the_ID(); ?>" <?php post_class('home'); ?>>
		<div id="page-content">
			<?php the_content(); ?>
		</div>
		<?php if(!is_front_page()) edit_post_link('<span>'.__("Edit").' </span>');  ?>
	</article>
<?php endwhile; ?>

<?php endif; ?>

<?php get_footer(); ?>