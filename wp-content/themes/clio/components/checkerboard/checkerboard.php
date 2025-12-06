<?php if(is_front_page()) {
    $component_tag = 'article';
    $header_tag = 'h1';
} else {
    $component_tag = 'section';
    $header_tag = 'h2';
} ?>
<<?php echo $component_tag; ?> class="row no-gutter component checkerboard<?php
    if($field['panels'][0]['acf_fc_layout']=='Text'&&$field['panels'][1]['acf_fc_layout']=='Image') echo ' pnp-swap';
    if(in_array($field['colour'], array('financeblue','telcogrey'))) echo ' '.$field['colour'];
    ?>">

    <?php  if($field['panels']) foreach($field['panels'] as $panel) { ?>
		<?php if($panel['acf_fc_layout'] == 'Image') { ?>
            <div class="col-xs-12 col-sm-6 checkerboard-cell image
            <?php if(substr($panel['background'],-3)=='png') echo 'png'; ?>
            <?php if(in_array($panel['alignment'], array('align-top','align-bottom'))) echo $panel['alignment'];  ?>"
             style="background-image:url(<?php echo $panel['background'] ?>);">
                <div class="height"><div class="ratio"></div></div>
            </div>
		<?php } ?>
        <?php if($panel['acf_fc_layout'] == 'Text') { ?>
            <div class="col-xs-12 col-sm-6 checkerboard-cell text">
                <?php if($field['anchor_text']) { ?><span class="sr-only header"><?php echo $field['anchor_text']; ?></span> <?php } ?>
                <div class="padding">
                    <?php if($panel['header']) {?>
                        <<?php echo $header_tag; ?>><?php echo apply_filters('the_title',$panel['header']); ?></<?php echo $header_tag; ?>>
                    <?php } ?>
                    <?php if($panel['copy']) {?>
                        <div class="copy">
                            <?php echo str_replace('<a ','<a class="btn btn-primary" ',$panel['copy']); ?>
                        </div>
                    <?php } ?>
                    <?php if(isset($panel['link'])) {?>
                        <a class="btn btn-primary <?php echo $class; ?>" href="<?php echo $href; ?>" role="button">
                            <?php echo ($panel['link_text'])? $panel['link_text'] : 'Find out more'; ; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
		<?php } ?>
	<?php } ?>
</<?php echo $component_tag; ?>>