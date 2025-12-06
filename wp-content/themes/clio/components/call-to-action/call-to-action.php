<?php // Create align class from block alignment settings
    if(isset($field['position']) && $field['position']!= 'center') { //centred is default, so ignore it
        $position_class = ' pos-'.$field['position'] . ' has-text-align-'.$field['position'];
    } else  $position_class = ' pos-center has-text-align-center';
    if(empty($field['background'])) $position_class .= ' empty-bg';
?>
<div class="<?php block_classes('call-to-action',$block,$position_class); ?>">
    <article class="col-xs-12 col-sm-12 window">
        <div class="ratio"></div>
        <div class="background" style="background-image:url(<?php echo $field['background']; ?>);"></div>
        <div class="content" style="background-image:url(<?php echo $field['background']; ?>);">
            <div class="valign">
                <div class="hgroup">
                    <h2 class="title"><?php echo $field['title']; ?></h2>
                    <?php if(!empty($field['text'])) { ?>
                        <p class="text"><?php echo $field['text']; ?></p>
                    <?php }; ?>
                    <a class="wp-block-button__link btn btn-primary" href="<?php echo $field['link']; ?>">
                        <?php echo !empty($field['button']) ? $field['button'] : 'Leer más'; ?>
                    </a>
                </div>
            </div>
        </div>
        <a class="overlay cta" href="<?php echo $field['link']; ?>"> </a>
    </article>
</div>
