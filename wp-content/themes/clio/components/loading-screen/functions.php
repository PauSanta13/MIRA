<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 05/02/16
 * Time: 19:10
 */


add_action('wp_head', 'print_loading_script');
function print_loading_script() {
    if(is_front_page() || !is_user_logged_in()) { 
        ?><script>document.documentElement.className = 'js preload';</script><?php
    }
}; 
