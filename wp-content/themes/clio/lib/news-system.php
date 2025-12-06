<?php
/**
 * This script helps to set a inner news page automatically
 * It is when called by the functions.php file
 * Converts the post #1 (hello world) in a static front-page
 * Converts the post #2 (about us) in the the blog home page
 * Also changes the menu label "Posts" for "News" in the dashboard
 */

// Setup post 1 to Home a post 2 to News when you switch on this theme
// This function is likely to be destructive
// as it clears the content and title of two pages
//add_action('after_switch_theme', 'news_setup_pages');
function news_setup_pages($slug = 'news') {
    $news_page = get_page_by_path($slug);
    if ($news_page) {
        return $news_page->ID;
    } else {
        // Set the home page
        $home_page = array(
            'ID'           => 1,
            'post_name' => 'home',
            'post_title'   => 'Home',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => get_bloginfo('name')
        );
        wp_update_post( $home_page );
        update_option('page_on_front', 1);
        update_option('show_on_front', 'page');

        // Set the news page
        $news_page = array(
            'ID'           => 2,
            'post_name' => $slug,
            'post_title' => 'News',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- -->',
        );
        wp_update_post($news_page);
        update_option('page_for_posts', 2);
        return 2;
    }
}

// Change Posts labels in the admin menu sidebar
add_action( 'admin_menu', 'news_post_menu_label' );
function news_post_menu_label() {
    global $menu;
    global $submenu;
    if($menu[5][0]) $menu[5][0] = 'Blog';
    /*
    $newsMenu = $menu[5];
    $menu[5] = $menu[6];
    $menu[6] = $newsMenu;
    */
    //if($submenu['edit.php'][5][0]) $submenu['edit.php'][5][0] = 'Entradas del blog';
    remove_menu_page( 'edit-comments.php' );
}

// Hides the News (or Posts) menu item in the admin menu sidebar
// In case you need to create a news system, remove this function
//add_action('admin_menu', 'hide_post_posttype');
function hide_post_posttype() {
    remove_menu_page( 'edit.php' );
}

// Remove tags from news
function news_unregister_tags() {
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'news_unregister_tags');