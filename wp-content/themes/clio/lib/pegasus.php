<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 21/01/2016
 * Time: 10:33
 */



/**
 * Append page slugs to the body class
 * NB: Requires init via add_filter('body_class', 'add_slug_to_body_class');
 *
 * @param
 *        	array
 * @return array
 */
function pegasus_bodyclasses($classes) {
	global $post;
	if (is_page () || is_singular ()) {
		$classes [] = sanitize_html_class ( $post->post_name );
	}
	/*if(is_page()) {
		array_push( $classes, 'context-'.get_field('colour'));
	}*/

	return $classes;
}
add_filter ( 'body_class', 'pegasus_bodyclasses' );

/**
 * Append page slugs to the body class
 * NB: Requires init via add_filter('body_class', 'add_slug_to_body_class');
 *
 * @param
 *        	array
 * @return array
 * @author Keir Whitaker
 */
function pegasus_postclasses( $classes ) {
	global $post;
	if (is_page () || is_singular ()) {
		$classes [] = sanitize_html_class ( $post->post_name );
	}

	if ( is_singular() ) {
		array_push( $classes, 'singular-post' );
	} else {
		array_push( $classes, 'archived-postsdf' );
	}

	return $classes;
}
add_filter( 'post_class', 'pegasus_postclasses' );


/**
*** This is an example function for setting up post types
**/
function pegasus_posttype($basicname,$title,$icon = 'dashicons-book',$public=true) { // Generally title is the plural name
	if($public) $supports = array('title','editor','excerpt','thumbnail'); // Removed: 'revisions'
	else $supports = array('title','editor','thumbnail');
	$args = array(
		'labels' => array(
				'name' => $title,
				'singular_name' => ucfirst($basicname) ,
				'add_new' => "Añadir $basicname",
				'add_new_item' => "Añadir nuevo $basicname",
				'new_item' => 'Nuevo'
		),
		'label' => ucfirst($basicname),
		'public' => $public,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => $icon,
		'rewrite' => array('slug' => $basicname, 'with_front' => false),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 6,
		'supports' => $supports,
		'has_archive' => false
	);
	register_post_type( $basicname , $args );
}


/**
 * Convert strings into colours
 *
 * Useful for give a random chosen but permanent setted color to anything (posts, categories, etc...)
 */
function str_to_color($str) {
	return '#' . substr ( md5 ( $str ), 0, 6 );
}

/**
 * Is this colours dark or light
 *
 * Useful to make text contrast, placing white over dark colours and black over light ones
 * $RGB has to be hexadecimal like #8f1ac2, #81c, 8f1ac2, 81c
 */
function is_color_dark($RGB = '') { 
	if(empty($RGB)) return false;
    if($RGB[0] == '#') $RGB = substr($RGB, 1); // remove hash (#)
    if (strlen($RGB) == 3) { // duplicate short notation 
      $RGB = $RGB[0].$RGB[0] . $RGB[1].$RGB[1] . $RGB[2].$RGB[2];
	}
	if(hexdec(substr($RGB,0,2))+hexdec(substr($RGB,2,2))+hexdec(substr($RGB,4,2))> 382){
		return false; //bright color
	} else {
		return true; //dark color
	}
}

/**
 * The compulsory thumbnail
 *
 * Allows to always have an image even if the thumbnail is not defined.
 * Please, change the fallback for the default image
 */
function the_compulsory_thumbnail($size = 'thumbnail',$display = 'echo', $postid = false) {
	if (!$postid) $postid = get_the_ID();
	if (has_post_thumbnail($postid) and $postid) {
		$id = get_post_thumbnail_id($postid);
	} elseif (!empty(get_post_meta($postid,'gallery_csv'))) {
		// Find img from postmeta fields
		$gal_csv = get_post_meta($postid,'gallery_csv');
		$gal_csv = explode(',', $gal_csv[0]);
		$found_imgurl = $gal_csv[0];
	} else { 
		/* Search img in content */
		$content = apply_filters('the_content', get_post_field('post_content', $postid));
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
		if(isset($matches[1][0]))$found_imgurl = $matches[1][0];
		if (empty($found_imgurl)) {
			/*If no image present in content, get first attached image*/
			$attachments = get_children(array('post_parent' => $postid, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order'));
			if ($attachments) {
				if (is_array($attachments)) {
					//$count = count($attachments);
					$first_attachment = array_shift($attachments);
					$id = $first_attachment->ID;
				}
			}
		}
	}
	if ($display == 'echo') {
		if(empty($found_imgurl) && !empty($id)) echo wp_get_attachment_image($id, $size);
		else echo '<img src="' . $found_imgurl . '" alt="" style="max-width:100%" />';
	} elseif ($display == 'return') {
		if(empty($found_imgurl) ) {
			if(!empty($id)) $src = wp_get_attachment_image_src($id, $size);
		} else {
			$src = array($found_imgurl);
		}
		if(isset($src))  return $src[0];
	}
}

// Allow SVG to be uploaded
add_filter('upload_mimes', 'cc_mime_types');
function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['vcf'] = 'text/vcard';
	return $mimes;
}

/**
 * Registering a main sidebar
 */
function pegasus_sidebar() {
	register_sidebar( array(
	'name' => __( 'Primary Sidebar', 'pegasus' ),
	'id' => 'primary-sidebar',
	'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'theme-slug' ),
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	) );
}
//add_action( 'widgets_init', 'pegasus_sidebar' );

// Security measure: remove wordpress version number from public files
add_filter('the_generator', 'pegasus_remove_version');
function pegasus_remove_version() {
    return '';
}

// Remove all the Emoji functionality in the frontend and backend
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Pass page slug in and it'll return its ID
 * (page post type only)
 */
function page_id_by_slug($page_slug)
{
	$page = get_page_by_path($page_slug);
	if ($page) {
		return $page->ID;
	} else {
		return null;
	}
}

/**
 * Pass string and an int, the string will be
 * cut off when the word count hits the limit
 * and have an ellipses appended to it
 */
function truncate_text($text, $limit)
{
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos = array_keys($words);
		$text = substr($text, 0, $pos[$limit]) . '...';
	}
	return $text;
}


function format_date($date)
{
	return date('d.m.Y', strtotime($date));
}


function the_content_enriched($text) {
	$text = explode("<br />",$text,2);
	$output = '<h1>'.$text[0].'</h1><p>'.$text[1].'</p>';
	$output = str_replace(
		array('((','))'),
		array('<span class="btn btn-primary" >','</span>'),
		$output
	);
	echo $output;
}

// Refresh the homepage cache
add_action( 'save_post', 'speedlightcache_refresh' );
function speedlightcache_refresh( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) return;
	$file = $_SERVER['DOCUMENT_ROOT'].'/z-home-cache.php';
	if(is_file($file) ) unlink($file);
}


/* To improve SEO we auto fill those images for which no alt text was specified with their file name
** So it is reccommended that file name takes the form of: "A descriptive name with spaces.jpg"
*/
add_filter( 'the_content', 'fill_empty_alt_with_filename', 6); 
function fill_empty_alt_with_filename($content) {
    if(!is_admin()) {
        $pattern = '/\/([^\.\/\"]+).([jpeng]+)" alt=""/i';
        $replacement = '/$1.$2" alt="$1"';
        $content =   preg_replace($pattern, $replacement, $content);
    }
    return $content;
}