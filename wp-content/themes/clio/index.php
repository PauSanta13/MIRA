<?php
$r301 = get_field('linkedpost');
if(is_404()) include(locate_template('lib/redirects.php'));
if(!empty($r301)) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$r301);
    exit();
}
get_header();
?>

<?php
if (!is_404() && have_posts()): ?>
    <?php 
        $headerFields = get_fields(get_option('page_for_posts')); // array['titulo','subtitulo']
        if(is_archive()) {
            $term = get_queried_object();
            $headerFields['titulo'] = apply_filters( 'single_cat_title', $term->name ); 
            if(is_category()) {
                $headerFields['subtitulo'] = ( empty($term->description) ? 'Contenidos de esta categoría' : $term->description);
                $catID = $term->term_id; 
            } else if(!empty($term->description)) {
                $headerFields['subtitulo'] = strip_tags($term->description);
            }
        } else if(is_search()) {
            $headerFields['titulo'] = __( 'Search' ); 
            $headerFields['subtitulo'] = wp_sprintf( __( 'Search results for &#8220;%s&#8221;' ) , get_search_query() );
        }
        if(get_query_var( 'paged' )) $headerFields['subtitulo'] = 'Página número '.get_query_var( 'paged' );
    ?>
    <article id="page-blog" class="page-blog">
        <?php the_component('encabezado',null,$headerFields); ?>
        <?php the_component('isotope-news-loop'); ?>
		<?php if(is_user_logged_in()) echo '<a class="post-edit-link" href="/wp-admin/edit.php"><span>Editar</span></a>';  ?>
    </article>

<?php else: ?>
    <article id="post-notfound" <?php post_class('text-center'); ?>>
        <header class="row banner-half empty">
            <hgroup class="pad text-center">
                <h1 class=" <?php if($fields['banner_image']!='cover') echo 'white'; ?>">ERROR 404</h1>
                <h2>No encontrado</h2>
                <?php echo get_search_form(); ?>
            </hgroup>
        </header>
        <p>Parece que no hay nada aquí.</p>
        <div><?php wp_nav_menu(); ?></div>
        <div class="search-area"><?php // get_search_form(); ?></div>
        <?php /*the_component('related-content', null, array($fields['related'][0] = array(
            'title' => 'Home page',
            //'background_image' => wp_get_attachment_url( get_post_thumbnail_id($prev_post->ID) ),
            'link_text' => 'Back to home page',
            'link_page' => get_bloginfo('url'),
        ))); */ ?>
        <br />
    </article>
<?php endif; ?>

<?php get_footer(); ?>