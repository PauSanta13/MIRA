<section class="row no-gutter component checkerboard testimonials slider-carousel">
    <span class="header sr-only">Testimonials</span>
    <?php if($field['selection']) { ?>
        <div class="image hidden-xs hidden-sm">
                <div class="bg show"></div>
        </div>
        <div class="content">
            <div class="padding">
                <div class="content-slider">
                    <?php foreach($field['selection'] as $key => $phrase) { ?>
                        <div data-key="<?php echo $key; ?>" class="text content-slide">
                            <p><?php echo $phrase['text']; ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</section>