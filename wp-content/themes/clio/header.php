<?php
if(!isset($_GET["callback"])) { ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js" style="background:white;min-height:1200px">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <title><?php wp_title(); ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
        <meta name="keywords" content="Podcast,Mona León Siminiani,Productora,Negra y Criminal,Cronoficción,Cuento de Navidad,Audio,Ficción,Ficción radiofónica,True Crime,Branded Content,Series,Sonoro,Sonora,Calidad,Producción,Audiovisual,Radioteatro,Documental,Audioficción,Ficción sonora">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries-->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
        <![endif]-->
        <link href="https://fonts.googleapis.com/css2?family=Sen:wght@400;700&display=swap" rel="stylesheet">
        <link rel="icon" href="/favicon.ico" >
        <link rel="icon" sizes="192x192" href="/apple-touch-icon.png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta name="theme-color" content="#FFFFFF">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">
        <?php echo get_field('tracking','option'); ?>
    </head>
<body <?php body_class(); ?>>
<header id="page-header">
    <?php the_component('loading-screen');?>
    <?php the_component('nav');?>
</header>
<div id="main-container" class="container">
    <main id="content" style="min-height:900px">
    <?php
} else {
    global $data;
    $data['title'] = wp_title(false,false);
    $data['bodyclasses'] = implode(' ',get_body_class());
    ob_start();
}
