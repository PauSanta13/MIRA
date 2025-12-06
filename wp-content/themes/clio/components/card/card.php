<?php

$post = $field[0]; // The post object should be array's first item 

// Get background url:
$background = the_compulsory_thumbnail('large','return', $post->ID);

$articleLink = get_permalink($post->ID);

$title = apply_filters('the_title',$post->post_title);

?>
<a class="component card <?php echo get_field('post_style',$post->ID); ?> <?php echo get_post_type($post); ?>" href="<?php echo $articleLink; ?>">
    <?php if(!empty($background) && !get_field('hide_archive')) { ?>
        <figure <?php if(empty($background)) echo ' class="no-bg"'; ?>>
            <img src="<?php echo $background; ?>" alt="<?php echo htmlspecialchars($title); ?>" />
        </figure>
    <?php }; ?>

    <header class="header">
        <?php $category = get_the_category($post->ID); ?>
        <strong class="title"><?php echo $title; ?></strong>
    <?php if(get_field('texto',$post->ID)) { ?>
        <span class="excerpt"><?php echo get_the_excerpt($post->ID); ?></span>
    <?php }; ?>

    <?php if(!empty($category))  { ?>
        <span <?php // href="echo get_category_link($category[0]->term_id)" ?> class="cat cat-<?php echo $category[0]->slug; ?>">
            <?php echo $category[0]->name ?>
        </span>
    <?php } ?>
    </header>
</a>