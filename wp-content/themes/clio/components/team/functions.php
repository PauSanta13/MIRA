<?php

// People Custom Post Type
add_action('init', 'team_block_posttype');
function team_block_posttype() {
    pegasus_posttype('experto','Equipo','dashicons-groups',false);
}