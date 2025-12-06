<section class="component component-contact-form">
    <figure class="img-mobile" style="display:none">
        <img src="<?php echo $field['img_mobile']; ?>" alt="(banner)">
    </figure>
    <div class="content">
        <div class="padding">
            <h1 class="general-heading">
                <?php echo $field['header']; ?>
            </h1>
            <p class="form-information"><?php echo $field['information']; ?></p>
            <div class="form-wrapper"> 
                <div class="img-desktop"
                    style="background-image:url('<?php echo $field['img_desktop']; ?>');"></div>
                <?php if(shortcode_exists('contact-form-7')) {
                    echo do_shortcode('[contact-form-7 id="'.$field['form'].'"]');
                } else { ?>
                    <div class="alert" stlye="color: red">Please install "Contact Form 7" to use this component.</div>
                <?php } ?>
                <div class="popup">
                    <div class="wrapper" style="max-height: 200px; overflow: auto;">
                        <button class="close-popup" style="display: none">✕</button>
                        <?php echo $field['popup']->post_content; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js"></script>

