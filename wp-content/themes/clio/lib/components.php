<?php
/**
 * Components functionality goes here
 */

/**
 * Advances Custom Fields functionality
 */

// Let's activate ACF PRO licence if it's not active
//add_action( 'plugins_loaded', 'activate_acf_pro' );
function activate_acf_pro() {
	if (function_exists('acf_pro_is_license_active') && !acf_pro_is_license_active()) {
		//$_POST['acf_pro_licence'] = 'b3JkZXJfaWQ9NTk4NzF8dHlwZT1kZXZlbG9wZXJ8ZGF0ZT0yMDE1LTA3LTEzIDA3OjQ0OjI2';
		include_once(PLUGINS_DIR . 'advanced-custom-fields-pro' . DS . 'pro' . DS . 'admin' . DS . 'settings-updates.php');
		$acfSettingsUpdate = new acf_settings_updates();
		$acfSettingsUpdate->activate_pro_licence();
	}
}

// Fetch the controllers and ACF blocks
add_action('acf/init', 'components_acf_init');
function components_acf_init() {
    $manifest_file = get_template_directory() . '/components/manifest.xml';
    $manifest = simplexml_load_file($manifest_file);
    if($manifest) {
        foreach ($manifest as $component) {
            if (isset($component->functions) && file_exists(locate_template('components/' . $component->name . '/' . $component->functions))) {
                include(locate_template('components/' . $component->name . '/' . $component->functions));
            }
            if (isset($component->block) && file_exists(locate_template('components/' . $component->name . '/' . $component->name . '.php'))) {
                if (function_exists('acf_register_block')) {
                    $blockcat =  ($component->block ? trim($component->block) : 'pegasus');
                    $blocktitle = ($component->title ? trim($component->title) : str_replace(array('-','_'),' ',ucfirst($component->name)));
                    $blockicon =  ($component->icon ? trim($component->icon) : 'plus-alt');
                    $blockdesc =  ($component->description ? trim($component->description) : _('Bloque personalizado'));
                    acf_register_block(array(
                        'name'            => $component->name,
                        'title'           => $blocktitle,
                        'description'     => $blockdesc,
                        'render_callback' => 'pegasus_acf_block_render_callback',
                        'category'        => $blockcat,
                        'mode'            => 'auto',
                        'icon'            => $blockicon,
                        'post_types'      => array('page','post')
                    ));
                } 
            }
        }
    } else {
        add_action( 'admin_notices', 'no_manifest_notice' );
        function no_manifest_notice() { ?>
            <div class="error notice">
                <p><?php _e( 'There is no "manifest.xml" in the components dir. This is needed for advanced components\' functionalities.' ); ?></p>
            </div>
        <?php }
    }
}

// Create a Gutemberg block category to place custom blocks, named as site's title
add_filter( 'block_categories', 'pegasus_block_category', 10, 2);
function pegasus_block_category( $categories, $post ) {
	return array_merge(
		array(
			array(
				'slug' => 'pegasus',
				'title' => get_bloginfo( 'name' ),
			),
			array(
				'slug' => 'pegasus-home',
				'title' => get_bloginfo( 'name' ) . ': ' . __('Homepage'),
			),
		),
		$categories
	);
}

// Create the function "the_component()" to render a component
if(!function_exists('the_component')){
    function the_component($name, $pageId = null, $field = array()) {
        $view = 'components/' . $name . '/' . $name . '.php';
        if(file_exists(locate_template($view))) {
            echo '<!-- Including ' . $name . ' component -->';
            include(locate_template($view));
        } else echo '<div><h3>There is no "' . $name . '.php". <a href="'.admin_url('admin.php?page=components&generate='.$name).'">Create it here</a>.</h3></div>';
    }
}

/** ACF Blocks **/
//require_once trailingslashit(get_stylesheet_directory()) . 'inc/acf-blocks.php';
function pegasus_acf_block_render_callback($block) {
    $name = str_replace('acf/', '', $block['name']);
    $view = 'components/' . $name . '/' . $name . '.php';
    if(file_exists(locate_template($view))) {
        echo '<!-- Including ' . $name . ' block -->';
        $field = get_fields();
        include(locate_template($view));
    } else {
        echo "Please create view of '{$name}'.";
    }
}

// Save custom fields as jsons in the /components directory
add_filter('acf/settings/save_json', function() {
    return get_stylesheet_directory() . '/components';
});
add_filter('acf/settings/load_json', function($paths) {
    $paths = array(get_template_directory() . '/components');
    return $paths;
});

// Prints component classes and also extract and prints wordpress gutemberg block classes
if(!function_exists('block_classes')){
    function block_classes($name, $block, $extraClasses = '') {
        // Create align class from block alignment settings
        $alignClass = isset($block['align'])? ' align-' . $block['align'] : '';
        if(!empty($extraClasses)) $extraClasses = ' '.$extraClasses;
        if(!empty($block['class'])) $extraClasses .= ' '.$block['class'];
        echo 'component component-' . $name . $alignClass . $extraClasses;
    }
}

// Synchronize components, source: support.advancedcustomfields.com/forums/topic/automatic-synchronized-json/
if(!function_exists('pegasus_sync_groups')){
    function pegasus_sync_groups($group_key = 'group_components',$sync = null) {
        $groups = acf_get_field_groups();
        $sync 	= array();
        if( empty( $groups ) )
            return;
        // find JSON field groups which have not yet been imported
        foreach( $groups as $group ) {
            $local 		= acf_maybe_get( $group, 'local', false );
            $modified 	= acf_maybe_get( $group, 'modified', 0 );
            $private 	= acf_maybe_get( $group, 'private', false );
            // ignore DB / PHP / private field groups
            if( $local !== 'json' || $private ) {
                // do nothing
            } elseif( ! $group[ 'ID' ] ) {
                $sync[ $group[ 'key' ] ] = $group;
            } elseif( $modified && $modified > get_post_modified_time( 'U', true, $group[ 'ID' ], true ) ) {
                $sync[ $group[ 'key' ] ]  = $group;
            }
        }
        // bail if no sync needed
        if( empty( $sync ) ) return false;
        else foreach( $sync as $key => $v ) {
            // append fields
            if( acf_have_local_fields( $key ) ) {
                $sync[ $key ][ 'fields' ] = acf_get_local_fields( $key );
            }
            // import
            acf_import_field_group( $sync[ $key ] );
            return true;
        }
    }
}
/*
// Enqueue recommendation block styles
function wpahead_recommendation_styles() {
    wp_enqueue_style('wpahead_recommendation_styles', get_stylesheet_directory_uri() . '/dist/css/block-recommendation.css', array(), '1.0.0' );
}
add_action( 'enqueue_block_assets', 'wpahead_recommendation_styles' );
*/
