<?php
if(!isset($_GET["callback"])) { ?>
    </main>
    </div>
    <?php the_component('footer'); ?>
	<?php wp_footer(); ?>
<!--Microdata--><script type="application/ld+json">{
"@context": "http://schema.org",
"@type": "Organization",
"address": {
	"@type": "PostalAddress",
	"streetAddress": "C/ Cristóbal Bordiú, 42", 
	"addressLocality": "28003 Madrid"
},
"logo": "https://acimutpsicologia.com/wp-content/uploads/2020/04/Logo-Acimut-Vertical.jpg",
"email": "info@acimutpsicologia.com",
"telephone": "+34912320744",
"name": "Acimut Psicología",
"url": "https://acimutpsicologia.com"
}</script>
</body>
</html>
    <?php
} else {
    global $fields, $data,$sitepress;
    if($sitepress) $data['languages'] = $sitepress->get_language_selector();
    $data['menu'] = wp_nav_menu( array('menu' => 'primary', 'container'=> false, 'echo' => 0) );
    $data['contents'] = preg_replace(array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s'), array('>','<','\\1'),ob_get_contents());
    ob_end_clean();
    echo preg_replace("/[\W]/",'',$_GET["callback"]).'('.json_encode($data).');';
}