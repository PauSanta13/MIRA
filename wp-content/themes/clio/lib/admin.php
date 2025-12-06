<?php
/**
 * This file includes the customizations for the backend
 */

// Set a admin stylesheet
add_action('admin_enqueue_scripts', 'pegasus_enqueue_admin_scripts');
function pegasus_enqueue_admin_scripts() {
    if (is_file(get_template_directory() . '/assets/css/admin.css')) {
        wp_register_style('admin_global_css', get_template_directory_uri() . '/assets/css/admin.css', null, null);
        wp_enqueue_style('admin_global_css');
    }
    if (is_file(get_template_directory() . '/assets/js/admin.js')) {
        wp_register_script('admin_global_js', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), null, true);
        //wp_enqueue_script('admin_global_js');
    }
    if (is_file(get_template_directory() . '/assets/css/admin-theme.css')) {
        wp_register_style('admin_theme_css', get_template_directory_uri() . '/assets/css/admin-theme.css', null, null);
        //wp_enqueue_style('admin_theme_css');
    }

    wp_register_style('admin_ionicons', 'http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', null, null);
    //wp_enqueue_style('admin_ionicons');
}


// Add Dashboard widgets to the wordpress backend home
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('custom_help_widget', 'Theme Options', 'custom_dashboard_help');
    wp_add_dashboard_widget('pegasus_pages_widget', __('Pages'), 'pegasus_pages_widget');
}

// Dashboard widget to clean src files and refresh cache
function custom_dashboard_help() {
    $pegasus = get_template_directory();
    $dir = get_template_directory().'/components/';
    echo '¡Welcome to '.get_bloginfo('name').'! <br />';
    if(substr($_SERVER['HTTP_HOST'], -3)=='dev' || substr($_SERVER['HTTP_HOST'], -2)=='lh') {
        echo '<strong>Running locally. Keep up the good work!</strong>';
    } elseif(substr($_SERVER['HTTP_HOST'], -12)=='surgever.com') {
        echo 'Beta site';
    } else {
        // Check if  the folder with source code has been deleted 
        if(is_dir($pegasus.'/src/')) {
            echo 'You must delete <code>/src/</code> directory.<br />';
        }
        /* Check if there are componets source js or css that should be deleted  */
        $partsLefts = array();
        $components_dir = scandir($dir);
        if(isset($_GET['action']) && $_GET['action'] == 'delete-sourcefiles') {
            $deleteLog = '';
        }
        if($components_dir) foreach($components_dir as $n){
            if(strpos($n,'.')===false) {
                if(file_exists($dir.$n.'/'.$n.'.scss')) {
                    if(isset($deleteLog)) {
                        if(unlink($dir.$n.'/'.$n.'.scss')) $deleteLog .= $n.'.scss ' ;
                        else echo 'Error: '. $dir.$n.'/'.$n.'.scss';
                    } else $partsLefts[] = $n.'.scss';
                }
                if(file_exists($dir.$n.'/'.$n.'.js')) {
                    if(isset($deleteLog)) {
                        if(unlink($dir.$n.'/'.$n.'.js')) $deleteLog .= $n.'.js ' ;
                        else echo 'Error: '. $dir.$n.'/'.$n.'.js';
                    } else $partsLefts[] = $n.'.js';
                }
            }
        }
        if(!empty($deleteLog)) {
            echo '<p>Delete sourcefiles finished running: <code>'. $deleteLog.'</code></p>';
        }  else if(!empty($partsLefts)) {
            echo '<p>You must delete '.count($partsLefts).' unnecessary files: <code>'. implode(', ',$partsLefts).'</code></p>';
            echo '<p><a class="button" href="?action=delete-sourcefiles">Borrar archivos fuente</a></p>';
        } 
        // Check cache freshness
        $cacheFile = $_SERVER['DOCUMENT_ROOT'].'/z-home-cache.php';
        if(isset($_GET['action']) && $_GET['action'] == 'delete-homecache') {
            if(is_file($cacheFile)) {
                if(unlink($cacheFile)) echo 'Ok! El cache fue borrado. ';
                else echo 'Error al intentar borrar el cache. ';
            } else echo 'No se borró el cache home porque no se encontró el archivo. ';
        }
        if(is_file($cacheFile) ) {
            $cacheAge =  (time() - filemtime($cacheFile)) ;
            $splitTime = $cacheAge>=3600 ? intval($cacheAge / 3600).' horas ' : intval($cacheAge / 60).' min '.($cacheAge % 60).' s';
            echo '<p><code><small>Home-cache ('.filesize($cacheFile).' bytes)</small></code> generado hace '. $splitTime
                . '.</p> <p><a class="button" href="?action=delete-homecache">Refrescar caché</a></p>'; //delete: unlink($file);
        } else {
            echo '<p>Cache home no existe. </p>';
            if(isset($_GET['action']) && $_GET['action'] == 'delete-homecache') {
                //si está el parámetro get debemos quitarlo
                echo '<a href="?">Actualizar esta página</a>';
            } else echo 'Para generarlo ahora, <a href="'.get_bloginfo('url').'" target="_blank">ve a la home y actualiza</a>.';
        }
    }
}

// Dashboard widget with direct access to pages
function pegasus_pages_widget() {
    $pages = get_posts(array('numberposts'=>21,'post_type'=>'page','orderby'=>'modified','order'=>'DESC'));
    $list = '<table style="width:100%" class="widget-pages"><tr><th>'.__('View').'</th><th>'.__('Edit').'</th><th>'.__('Last Modified').'</th></tr>';
    $frontID = get_option('page_on_front'); 
    foreach ($pages as $i=>$page) {
        if($i>20) break;
        $list .= '<tr><td><a href="'.get_permalink($page->ID).'" target="_blank"><span class="dashicons view '.($page->ID==$frontID?'dashicons-admin-home':'dashicons-media-default').'"></span></a></td>';
        $list .= '<td><a href="post.php?action=edit&post='.$page->ID.'">'.$page->post_title.' <i class="dashicons edit"></i></a></td>';
        $list .= '<td><span>'. human_time_diff( date('U', strtotime($page->post_modified)), current_time('timestamp') ) .'</span></td></tr> ';
    }
    if(count($pages) > 20) echo $list .= '<tr><td><a href="edit.php?post_type=page">'.__('View more').'</a>';
    echo '<ul><li>'.$list.'</table></li></ul>'; //
}

// Display "local" for .dev or .lh and "test" for pnpd.co.uk
add_action('admin_head', 'test_domain_highlighting');
function test_domain_highlighting() {
    $abitem = '#wpadminbar #wp-admin-bar-site-name>a.ab-item';
    echo '<style>';
    if(substr($_SERVER['HTTP_HOST'], -3)=='dev' || substr($_SERVER['HTTP_HOST'], -2)=='lh') {
        echo "$abitem {color: YellowGreen} $abitem::after {content:' local';font-size:70%} $abitem::before{color: Green;content:'\\f472'!important}";
    } elseif(substr($_SERVER['HTTP_HOST'], -12)=='surgever.com') {
        echo "$abitem {color: Gold} $abitem::after {content:' testsite';font-size:70%} $abitem::before{color: Orange;content:'\\f115'!important}";
    } else {
        echo "$abitem::before{content:'\\f319'!important}";
    }
    echo '</style>';
}

// Add custom post types to the "at a glance" widget
add_action( 'dashboard_glance_items', 'cpad_at_glance_content_table_end' );
function cpad_at_glance_content_table_end() {
    $args = array('show_ui' => true,'_builtin' => false);
    $output = 'object';
    $operator = 'and';

    $post_types = get_post_types( $args, $output, $operator );
    foreach ( $post_types as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
        if ( current_user_can( 'edit_posts' ) ) {
            $output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
            echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
        }
    }
}

// Removes useless dahsboard widgets
add_action( 'admin_init', 'remove_dashboard_meta' );
function remove_dashboard_meta() {
    remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' ); // Wordpress News
    //remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); //Quick draft
    //remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); //At a Glance
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');  //Activity
    // Outdated widgets:
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}



// Admin footer modification
add_filter('admin_footer_text', 'pnpd_footer_admin');
function pnpd_footer_admin () {
    echo '<span id="footer-thankyou">Powerered by <a href="http://surgever.com" target="_blank">Surgever</a></span>';
}

// Remove advanced options from the menu
add_action( 'admin_menu', 'production_hide_menuitems',99 );
function production_hide_menuitems() {
    // Only hide the items if it's not local (.dev or .lh)
    add_submenu_page( 'index.php', 'Home', 'Home', 'read', get_bloginfo('url'));
    if(substr($_SERVER['HTTP_HOST'], -3)!='dev' && substr($_SERVER['HTTP_HOST'], -2)!='lh') {
        // remove_menu_page( 'themes.php' );
        remove_menu_page( 'plugins.php' );
        remove_menu_page( 'tools.php' );
        remove_menu_page( 'gmw-add-ons' ); // Plugin: GEO my WP
        remove_menu_page( '/admin.php?page=meowapps-main-menu' ); // Plugin: meowapps
        remove_menu_page( 'edit.php?post_type=acf-field-group' ); // Plugin: ACF
        remove_submenu_page( 'options-general.php','options-discussion.php' ); // Plugin: ACF

    }
}

//Disable heartbeat
remove_action( 'admin_init', 'wp_auth_check_load' );



/* 
** COLUMNS HANDLING
*/


// Tidy up the backend's menu CPT links
add_action('admin_menu', 'lasonora_adminmenus');
function lasonora_adminmenus() {
    // Move programs under podcast:
    //remove_menu_page('edit.php?post_type=program' );
    //add_submenu_page('edit.php?post_type=podcast', 'Programas', 'Programas', 'edit_posts', 'edit.php?post_type=program' );

    //remove_menu_page('edit.php' );
    //remove_menu_page('edit-comments.php' );
}


// Show the thumbnail images in columns of the dashboard
add_action('manage_posts_custom_column', 'posts_custom_columns', 5, 2); // this define columns content globally
//add_filter('manage_program_posts_columns', 'podcast_columns_set', 5); //this adds columns to this CPT
add_filter('manage_podcast_posts_columns', 'podcast_columns_set', 5); //this adds columns to this CPT
add_filter('manage_posts_columns', 'illustrated_columns_set', 5); //this adds columns to this CPT
function illustrated_columns_set($columns){
    unset( $columns['author'] );
    //unset( $columns['categories'] );
    $columns['thumbnail'] = __( 'Thumbnail' );
    return $columns;
}
function podcast_columns_set($columns){
    unset( $columns['author'] );
    unset( $columns['categories'] );
    $columns = array_merge( //add col "number" in position 1
        array_slice($columns, 0, 1),
        array('number' => '&#8470;'),
        array_slice($columns, 1)
    );
    $columns['thumbnail'] = __( 'Thumbnail' );
    return $columns;
}
if(!function_exists('posts_custom_columns') ) {
    function posts_custom_columns($column_name, $id) {
        if ($column_name === 'thumbnail') {
            echo the_compulsory_thumbnail('thumbnail','echo', $id);
        } elseif ($column_name === 'number') {
            echo '<span style="color:#000">0'.get_field('no').'</span>';
        } elseif ($column_name === 'egpl_actions' && get_post_status($id)=='publish') {
            //functionname($id);
        } elseif ($column_name === 'logo') {
            if (get_field('logo')) echo '<img src="' . get_field('logo') . '" width="76px" />';
            else echo '-';
        }
    }
}

add_action('manage_pages_custom_column', 'pages_custom_columns', 5, 2); // this define PAGE columns content globally
add_action('manage_pages_columns', 'page_columns_set', 5); //this adds columns to pages
function page_columns_set($columns){
    return array(
        'cb' => '<input type="checkbox" />',
    //    'sub_banner_title' => __('Subtitle'),
        'title' => __('Title'),
        'date' => __('Date')
    );
}
if(!function_exists('pages_custom_columns') ) {
    function pages_custom_columns($column_name, $id) {
        if ($column_name === 'theme') {
            echo the_post_thumbnail(array(36,36)) . ' ' . get_field('colour');
        } elseif ($column_name === 'sub_banner_title') {
            $color = get_field('colour');
            $subtitle = get_field('sub_banner_title') ? get_field('sub_banner_title') : '<i style="opacity:0.6">(None)</i>';
            $hex = array(
                'green'=> '#0aa456',
                'blue'=> '#1db3d6',
                'dark-blue'=> '#00587c',
                'light-green'=> '#6ab600',
                'black'=> '#000',
                'none'=> '#ccccc',
            );
            echo '<small>'.$subtitle.'</small>';
        }
    }
}


// Add image size to media library columns
add_filter( 'manage_upload_columns', 'wpse_237131_add_column_file_size' );
add_action( 'manage_media_custom_column', 'wpse_237131_column_file_size', 10, 2 );

function wpse_237131_add_column_file_size( $columns ) { // Create the column
    $columns['filesize'] = __('Size');
    unset( $columns['author'] );
    unset( $columns['comments'] );
    return $columns;
}
function wpse_237131_column_file_size( $column_name, $media_item ) { // Display the file size
    if ( 'filesize' != $column_name || !wp_attachment_is_image( $media_item ) ) {
      return;
    }

    $file = get_attached_file( $media_item );
    $meta     = wp_get_attachment_metadata( $media_item );
	if ( isset( $meta['filesize'] ) ) {
		$file_size = size_format($meta['filesize']);
	} elseif ( file_exists( $file ) ) {
		$file_size = size_format(filesize( $file ) );
    } else $file_size = 0;
    if( $file_size && isset( $meta['width'] ) && isset( $meta['height'] ) ) {
        $file_size .= ' <small style="display:block">(' . $meta['width'] . 'x' . $meta['height'] . ')</small>';
    }
    echo $file_size;
}