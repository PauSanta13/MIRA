<?php get_header(); 
$fields = get_fields(); 
?>

<?php if (have_posts()): while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <a href="<?php echo get_permalink( get_option('page_for_posts' ) ); ?>" class="blog-volver">Blog</a>
        <h1><?php the_title(); ?></h1>
        <p class="fecha">
            Publicado el <?php the_date(); ?>
            <?php foreach(get_the_category() as $cat) { // catlink: get_term_link($cat, 'category'); ?>
                - <a href="<?php echo get_category_link($cat->term_id ); ?>" ><?php echo $cat->name; ?></a>
            <?php } ?> 
        </p>
        <section class="content">
            <?php if(!get_field('hide_single')) { ?>
            <?php
                $gal_type = get_field('gallery_type');
                if(get_post_thumbnail_id() && $gal_type!='gallery' && $gal_type!='csv') {
                    // For SEO get and clean the image title (which by default is the file name))
                    $thumbTitle = ucfirst(str_replace(array('_','-'),' ',get_the_title(get_post_thumbnail_id())));
                    $thumbSrc = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
                    $thumbImg = get_the_post_thumbnail();
                } elseif($gal_type!='gallery') {
                    $thumbTitle = get_the_title();
                    $gal_csv = get_post_meta($post->ID,'gallery_csv');
                    $gal_csv = explode(',', $gal_csv[0]);
                    $thumbSrc = $gal_csv[0];
                    $thumbImg = '<img src="' . $thumbSrc . '" alt="" style="max-width:100%" />';
                }
            ?>
            <figure class="thumbnail <?php
                echo (get_field('big') ? 'big' : 'normal');
                if($gal_type=='gallery' || ($gal_type=='csv' && count($gal_csv)>1)) echo ' slider'; ?>">
                <?php
                    if($gal_type=='gallery') {
                        // It is a acf slider gallery
                        foreach(get_field('gallery_acf') as $img) {
                            echo '<a href="'.$img['url'].'" class="noajax" target="_blank">'
                                .'<img src="'.$img['url'].'" class="slide acf" alt="'.$img['title'].'" /></a>';
                        }
                    } elseif(isset($gal_csv) && count($gal_csv)>1) {
                        // It is a csv slider gallery
                        foreach($gal_csv as $img) {
                            if(!empty($img)) echo '<a href="'.trim($img).'" class="noajax" target="_blank">'
                                .'<img src="'.trim($img).'" class="slide" alt="'.$thumbTitle.'" /></a>';
                        }
                    } else if(!empty($thumbSrc)) {
                        // If the alt attribute is empty, fill it with the img title
                        echo '<a href="'.$thumbSrc.'" class="noajax" target="_blank">'
                            .str_replace('alt=""','alt="'.$thumbTitle.'" title="'.$thumbTitle.'"', $thumbImg).'</a>';
                    } else echo 'No se ha establecido ninguna imagen para esta entrada.'
                ?>
            </figure>
            <?php } ?>
            <?php the_content(); ?>
        </section>
        <?php if(empty($fields['hide_cat']) ) { ?>
            <div class="final">
                <?php foreach(get_the_category() as $cat) { // catlink: get_term_link($cat, 'category'); ?>
                    <a class="btn btn-primary cat" href="<?php echo get_category_link($cat->term_id ); ?>" >
                    <?php echo $cat->name; ?>
                </a>
                <?php } ?>
            </div>
        <?php }; ?>
        <?php if(!is_front_page()) edit_post_link('<span>'.__("Edit").' </span>');  ?>
        <?php  
            $CTA = get_field('cta_post');
            if(empty($CTA)) {
                $posts = get_posts(array(
                        'numberposts' => 1,
                ));
                $CTA = $posts[0];
            }
            the_component('call-to-action',null,array(
                'position' => 'center align-full',
                'background' => the_compulsory_thumbnail('large','return', $CTA->ID),
                'link' => get_permalink($CTA->ID),
                'title' => apply_filters('the_title',$CTA->post_title),
                //'text' => get_the_excerpt($post->ID),
            ));
        ?>
	</article>
<?php endwhile; ?>

<?php endif; ?>

<?php get_footer(); ?>

