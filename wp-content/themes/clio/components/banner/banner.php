<?php
    // For SEO get and clean the image title (which by default is the file name))
    $thumbTitle = ucfirst(str_replace(array('_','-'),' ',get_the_title(get_post_thumbnail_id())));
    // If the alt attribute is empty, fill it with the img title
    $thumbImg = str_replace('alt=""','alt="'.$thumbTitle.'" title="'.$thumbTitle.'"',get_the_post_thumbnail());
    $thumbImg = str_replace('>',' style="opacity:'. (1 - (0.01 * get_field('overlay-opacity'))).'">', $thumbImg);
?>
<header class="row component-banner alignfull <?php echo (empty($thumbImg)) ? 'no-bg' : "has-bg"; ?>">
    <div class="vertical-align">
        <div class="overlay">
            <?php if(!empty($thumbImg)) { ?>
                <?php echo $thumbImg; ?>
            <?php } ?>
        </div>
        <div class="hgroup">
            <h1 class="fadeInUp"><?php the_title(); ?></h1>
        </div>
    </div>
</header>