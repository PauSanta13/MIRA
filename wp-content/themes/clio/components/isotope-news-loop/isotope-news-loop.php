<section class="component isotope-news-loop blog">
    <nav class="nav filters">
        <?php // <strong class="desc">Seleccionar categoría:</strong> ?>
        <ul>
            <?php if(!is_home() || get_query_var( 'paged' )) { ?>
                <li><a href="<?php echo get_permalink( get_option('page_for_posts' ) ); ?>" class="btn btn-primary"><span>&laquo; Ver todo el blog</span></a></li><?php
            } else {
                ?><li class="<?php if(is_home()) echo 'active'; ?>">
                    <a href="<?php echo get_permalink( get_option('page_for_posts' ) ); ?>"
                    data-filter=".item" class="btn btn-primary"><span>Todo</span></a> 
                </li><?php 
            }
            global $catID;
            foreach(get_categories() as $category) { //removing white space between the <li>'s
                if(substr($category->name,0,1) == '_') continue; // if name starts with "_" should be hidden
                ?><li class="<?php if(isset($catID) && $catID == $category->term_id) echo 'active'; ?>"><a
                    href="<?php echo get_category_link($category->term_id); ?>" data-filter=".cat-<?php echo $category->slug; ?>"
                    class="btn btn-primary"><span><?php echo $category->name; ?></span></a></li><?php
            }?>
        </ul>
    </nav>
    <div class="isotope">
        <?php while (have_posts()) : the_post(); ?>
            <?php $postID = (isset($post) && isset($post->ID) ? $post->ID : null) ; ?>
            <?php $categories = get_the_category($postID); ?>
            <?php $catClass = ''; foreach($categories as $cat) $catClass .= ' cat-'.$cat->slug;?>
            <div class="item <?php echo $catClass; ?>">
                <?php global $post; the_component('card', null, array($post)); ?>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="pag-wrap clear text-center">
    <?php get_template_part('pagination'); ?>
    </div>
</section>