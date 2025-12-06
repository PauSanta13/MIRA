<?php
/**
 *  functions specifics to this project
 *
 * @package WordPress
 * @subpackage pegasus
 */


// Theme colours  (Used for Gutemberg, Sass and ACF) 
$themeColorNames = array(
	'light-grey', 'acimut-light', 'acimut', 'acimut-dark','dark-grey','crema-claro', 'crema-oscuro', 'marino', 'rosa', 'lila', 'lila-claro'
);
$themeColors = <<<COLORS
    #dddddd, #c1f4ee, #88e2d6, #1b9b8b, #2a2a2a, #f6eddc, #e6bc74, #456db3, #e4acaf, #989fea, #e0e5fd
COLORS;
$themeColors = explode(',',$themeColors);

/**
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 *
 */
function pegasus_setup() {
	if ( ! isset( $content_width ) ) $content_width = 900;
	global $lang,$themeColorNames,$themeColors;
	$lang = 'es';
	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );
    add_theme_support( 'align-wide' );
	//add_theme_support( 'automatic-feed-links' );
    add_theme_support('menus');
	// The number of locations this theme uses wp_nav_menu().
	register_nav_menus ( array ( 'topleft' => 'Lado izquierdo' ) );
	register_nav_menus ( array ( 'topright' => 'Lado derecho' ) );
    // This site's colour palette for gutemberg editor
    $gutembergPallete = array();
    foreach($themeColorNames as $i => $name) {
        $gutembergPallete[] = array('name'  => esc_html__( ucfirst(str_replace('-',' ',$name)), 'pegasus' ),'slug' => $name,'color' => trim($themeColors[$i]));
    }
    add_theme_support('editor-color-palette', $gutembergPallete); 

    add_theme_support( 'html5', array('search-form', 'comment-form', 'caption') );
    add_filter('show_admin_bar', '__return_false');
    
    // This site's colour palette for ACF colour picker
    add_action( 'acf/input/admin_footer', 'pegasus_ACF_color_palette' );
    function pegasus_ACF_color_palette() { global $themeColors; ?>
        <script type="text/javascript">
        (function($) {
            acf.add_filter('color_picker_args', function( args, $field ){
                // add the hexadecimal codes here for the colors you want to appear as swatches
                args.palettes = [<?php foreach($themeColors as $color) echo "'$color', " ?>] 
                // return colors
                return args;
            });
        })(jQuery);
        </script>
    <?php }

    register_block_style(
        'core/columns',
        array(
            'name'  => 'no-margin',
            'label' => __( 'Sin margen', 'pegasus' ),
            'inline_style' => '.is-style-no-margin {padding: 0 !important; margin-bottom: 0;} .is-style-no-margin .wp-block-column {margin-bottom: 0;margin-left:0}',
        )
    );
}
add_action( 'after_setup_theme', 'pegasus_setup' );


/**
 * Enqueue all JS and CSS needed in the theme
 */
add_action('wp_enqueue_scripts', 'pegasus_enqueue_scripts_and_styles');
function pegasus_enqueue_scripts_and_styles()
{

	wp_deregister_script('jquery');
	wp_deregister_style('bootstrap');
	if (substr($_SERVER['HTTP_HOST'], -3) != 'dev' && substr($_SERVER['HTTP_HOST'], -2) != 'lh') :
        // Register from CDN
        wp_register_script('jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', null, null, false);
        wp_register_script('jquery-ui', ("//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"), false);
        wp_register_script('modernizer', '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js', null, null);
        wp_register_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css', null, null);
    else :
        // Register from local
        wp_register_script('jquery', 'http://local-cdn.lh/js/jquery.2.2.4.min.js', null, null, false);
        wp_register_script('jquery-ui', ("http://local-cdn.lh/js/jquery-ui.1.8.9.min.js"), false);
        wp_register_script('modernizer', 'http://local-cdn.lh/js/modernizr.2.8.3.js', null, null);
        wp_register_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', null, null);
	endif;
    // Enqueue styles
    wp_enqueue_script('jquery');
	wp_enqueue_style('bootstrap');
    // Javscript files 
	wp_register_script('global_js', get_template_directory_uri() . '/assets/js/main-15.min.js', array('jquery'), null, true);
	wp_localize_script('global_js', 'ajaxNavData', array('url' => get_bloginfo('url')) );
	wp_enqueue_script('global_js');
    // CSS files 
	wp_register_style('global_css', get_template_directory_uri() . '/assets/css/main-24.min.css' , array('bootstrap'), null);
	wp_enqueue_style('global_css');
}

// Add ascii markup to titles, replacing stars for bold and not show em in the backend or head's title
if(is_admin()) {
    add_filter( 'the_title', 'bcsg_cleanup_title',10,2 ); // Backend
} else {
    add_filter( 'the_title', 'bcsg_markup_title' ); // Front end
    add_filter( 'wp_title', 'bcsg_cleanup_title',10,2 ); // <title>
}
function bcsg_markup_title( $title, $id = null) {
    // Show pipe as new line:
    if (strpos($title, '|') !== FALSE) {
        $title = preg_replace('/\|/', '<small>', $title, 1).'</small>';
    }
    // Show colon as supertitle:
    if (strpos($title, ':') !== FALSE) {
        $title = preg_replace('/^([^:]+?):/', '<small>$1</small>', $title, 1);
    }
    // Show stars as strong:
    return preg_replace('/\*([^*]+?)\*/', '<strong>$1</strong>',$title);
};
function bcsg_cleanup_title( $title, $id = null) {
    // Remove things after the pipe:
    if (strpos($title, '|') !== FALSE) {
        $title = preg_replace('/\|.*/', '', $title, 1);
    }
    // Don't show stars on titles:
    return str_replace(array('*','|'), '', $title);
};


// Customize the wordpress login for a more corporative experience
add_action( 'login_enqueue_scripts', 'rapleys_login_css' );
function rapleys_login_css() { ?>
    <style type="text/css">
        body {background: linear-gradient(180deg, #233961 70% , #1d3156) !important}
        #login h1 {height: 120px;margin: 0 20px;background: url(<?php bloginfo('template_url'); ?>/assets/img/header/sphere.png) center no-repeat;
            background-size: contain;}
        #login h1 a {display:none}
        .wp-core-ui .button-primary {border-radius: 30px}
    </style>
<?php }

/**
 * Images functionality
 */
// Customize image sizes for the theme so that they are cropped to the needed sizing
add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');
function remove_default_image_sizes($sizes) {
	update_option( 'thumbnail_size_w', 100 );
	update_option( 'thumbnail_size_h', 100 );
	update_option( 'thumbnail_crop', 1 );
	update_option( 'medium_size_w', 400 );
	update_option( 'medium_size_h', 300 );
	update_option( 'medium_crop', 0 );
	unset($sizes['medium_large']);
	unset($sizes['1536x1536']);
	unset($sizes['2048x2048']);
	return $sizes;
}
 


//wpseo_pre_analysis_post_content

// The reveal function is a shortcut for var_dump() or print_r()
if(!function_exists('reveal')) {
    function reveal($value) {
        echo '<textarea style="position:fixed;z-index:1000;background:lightblue;color:darkblue;bottom:20px;left:20px;overflow:auto;width:400px;height:300px" class="reveal_pre">';
        print_r($value);
        echo '</textarea>';
    }
}

// Disable feed and feed links in the source code
add_action('do_feed', 'itsme_disable_feed', 1);
add_action('do_feed_rdf', 'itsme_disable_feed', 1);
add_action('do_feed_rss', 'itsme_disable_feed', 1);
add_action('do_feed_rss2', 'itsme_disable_feed', 1);
add_action('do_feed_atom', 'itsme_disable_feed', 1);
add_action('do_feed_rss2_comments', 'itsme_disable_feed', 1);
add_action('do_feed_atom_comments', 'itsme_disable_feed', 1);
function itsme_disable_feed() {
    wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
}
// Disable comment feeds.
add_action( 'do_feed_rss2_comments', 'disable_feeds', -1 );
add_action( 'do_feed_atom_comments', 'disable_feeds', -1 );
// Prevent feed links from being inserted in the <head> of the page.
add_action( 'feed_links_show_posts_feed',    '__return_false', -1 );
add_action( 'feed_links_show_comments_feed', '__return_false', -1 );
remove_action( 'wp_head', 'feed_links',       2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'index_rel_link' ); // index link
remove_action( 'wp_head', 'wp_resource_hints', 2 ); // dns-prefetch s.w.org link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' ); // oembed Post Links 
remove_action( 'wp_head', 'rest_output_link_wp_head'); // WP Json Rest Api link 
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
add_filter( 'wpseo_debug_markers', '__return_false' ); // Remove Yoast SEO html comments in wp_head




// Create a footer admin page
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title'=>'Footer',
	//	'capability' 	=> 'manage_options',
		'parent_slug' => 'themes.php'
	));
}


require_once("lib/news-system.php");
require_once("lib/components.php");
require_once("lib/pegasus.php");
require_once("lib/editor-styles.php");
require_once("lib/admin.php");
//require_once("lib/search.php");
require_once("lib/components-setup.php");
require_once("lib/imgdir-view.php");
//require_once("lib/locale.php");


