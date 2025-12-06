<section class="component component-testimonials aligncenter">
    <div class="slider">
    <?php if(!empty($field['selection'])) { 
        foreach($field['selection'] as $key => $phrase) { ?>
            <div data-key="<?php echo $key; ?>" class="testimonial <?php if(strlen($phrase['text']) > 50) { echo 'largo'; }; ?>">
                <p><?php echo (empty($phrase['text']) ? '(Escribe la frase aquí...)' : $phrase['text']); ?></p>
            </div>
        <?php }
    } else {
        echo '<em> (Por favor, introduzca algún contenido aquí.)</em>';
    } ?>
    </div>
</section>

