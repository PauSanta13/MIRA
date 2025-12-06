<?php
/**
 * Editor styling goes here
 * Use carefully, the editor should have the least styling possible
 * Important styling classes are better applied on the view file instead in the backend
 */

// Enqueue the wysiwyg editor stylesheet
add_action('after_setup_theme', 'pegasus_add_editor_styles');
function pegasus_add_editor_styles() {
	// Add support for editor styles.
	add_theme_support( 'editor-styles' );
    // Enqueue editor styles.
    if (is_file(get_template_directory() . '/assets/css/editor.css')) {
        add_editor_style( get_template_directory_uri() . '/assets/css/editor.css' );
    }
}

// Enable custom WYSIWYG styles dropdown
add_filter('mce_buttons_2', 'wpb_mce_buttons_2');
function wpb_mce_buttons_2($buttons) {
    array_unshift($buttons, 'styleselect');
    return $buttons;
}

// Set the options for the TinyMCE dropdown
add_filter('tiny_mce_before_init', 'my_mce_before_init_insert_formats');
function my_mce_before_init_insert_formats($init_array)	{
    // Define the style_formats array
    $style_formats = array(
        // Each array child is a format with it's own settings
        array(
            'title' => 'SubHead',
            'block' => 'h3',
            'classes' => 'subhead',
            'wrapper' => false,
        ),
        array(
            'title' => 'Header',
            'block' => 'h2',
            'classes' => 'header',
            'wrapper' => false,
        ),
        array(
            'title' => 'Button',
            'block' => 'a',
            'classes' => 'btn btn-primary',
            'wrapper' => false,
        ),
    );
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode($style_formats);
    return $init_array;
}